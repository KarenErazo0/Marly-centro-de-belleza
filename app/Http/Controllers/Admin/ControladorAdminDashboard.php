<?php

namespace App\Http\Controllers\Admin;
use Cloudinary\Cloudinary;
use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\ConfiguracionSitio;
use App\Models\Servicio;
use App\Models\Trabajador;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ControladorAdminDashboard extends Controller
{
    public function index(Request $request): View
    {
        Carbon::setLocale('es');

        $hoy = Carbon::today();
        $fechaSeleccionada = $request->query('fecha') ? Carbon::parse($request->query('fecha')) : $hoy->copy();
        $mesActual = $request->query('mes') ? Carbon::createFromFormat('Y-m', $request->query('mes'))->startOfMonth() : $fechaSeleccionada->copy()->startOfMonth();

        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();

        $busqueda = trim((string) $request->query('buscar', ''));
        $tab = in_array($request->query('tab'), ['configuracion', 'servicios', 'personal', 'citas', 'clientes'], true)
            ? $request->query('tab')
            : 'personal';

        $citasPorDia = Cita::whereBetween('fecha_cita', [$inicioMes->format('Y-m-d'), $finMes->format('Y-m-d')])
            ->selectRaw('fecha_cita, COUNT(*) as total')
            ->groupBy('fecha_cita')
            ->pluck('total', 'fecha_cita');

        $todasCitas = $this->consultaCitas($busqueda)
            ->orderByDesc('fecha_cita')
            ->orderByDesc('hora_inicio')
            ->get();

        $servicios = Servicio::with(['trabajadores' => fn ($q) => $q->orderBy('nombre_completo')])
            ->withCount(['citas', 'detalles'])
            ->orderBy('nombre_servicio')
            ->get();

        $trabajadores = Trabajador::with('servicios')
            ->withCount(['citas', 'detalles'])
            ->orderBy('nombre_completo')
            ->get();

        $clientes = Cliente::orderByDesc('fecha_registro')->get();

        return view('admin.dashboard', [
            'tab' => $tab,
            'hoy' => $hoy,
            'fechaSeleccionada' => $fechaSeleccionada,
            'mesActual' => $mesActual,
            'mesAnterior' => $mesActual->copy()->subMonth()->format('Y-m'),
            'mesSiguiente' => $mesActual->copy()->addMonth()->format('Y-m'),
            'inicioCalendario' => $inicioMes->copy()->startOfWeek(Carbon::MONDAY),
            'finCalendario' => $finMes->copy()->endOfWeek(Carbon::SUNDAY),
            'citasPorDia' => $citasPorDia,
            'citasFechaSeleccionada' => $this->obtenerCitasPorFecha($fechaSeleccionada, $busqueda),
            'citasHoy' => $this->obtenerCitasPorFecha($hoy, $busqueda),
            'todasCitas' => $todasCitas,
            'busqueda' => $busqueda,
            'citasAgrupadas' => $this->agruparCitasPorTiempo($todasCitas),
            'servicios' => $servicios,
            'trabajadores' => $trabajadores,
            'clientes' => $clientes,
            'configuracion' => $this->configuracionSitio(),
        ]);
    }

 public function actualizarConfiguracion(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'contacto_ubicacion' => ['required', 'string', 'max:120'],
        'contacto_telefono' => ['required', 'string', 'max:30'],
        'contacto_correo' => ['required', 'email', 'max:120'],
        'contacto_horario' => ['required', 'string', 'max:500'],
        'instagram_url' => ['nullable', 'url', 'max:255'],
        'whatsapp_url' => ['nullable', 'url', 'max:255'],
        'hero_imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
    ], [], [
        'hero_imagen' => 'imagen principal',
        'contacto_ubicacion' => 'ubicación',
        'contacto_telefono' => 'teléfono',
        'contacto_correo' => 'correo',
        'contacto_horario' => 'horario',
    ]);

    $configuracion = $this->configuracionSitio();

    if ($request->hasFile('hero_imagen')) {
        $imagenAnterior = $configuracion->hero_imagen;

        $validated['hero_imagen'] = $this->guardarImagen($request, 'hero_imagen', 'site', 'hero-admin');

        $configuracion->fill($validated)->save();

        $this->eliminarImagenPublica($imagenAnterior, 'images/site/');
    } else {
        $configuracion->fill($validated)->save();
    }

    return redirect()
        ->route('admin.dashboard', ['tab' => 'configuracion'])
        ->with('success', 'La página principal fue actualizada correctamente.');
}
    public function guardarServicio(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->reglasServicio(true), [], $this->atributosServicio());

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $this->guardarImagen($request, 'imagen', 'services', 'admin-service');
        }

        $validated['estado'] = $request->boolean('estado') ? 'activo' : 'inactivo';

        Servicio::create($validated);

        return redirect()
            ->route('admin.dashboard', ['tab' => 'servicios'])
            ->with('success', 'Servicio agregado correctamente.');
    }

public function actualizarServicio(Request $request, Servicio $servicio): RedirectResponse
{
    $validated = $request->validate($this->reglasServicio(false), [], $this->atributosServicio());

    $validated['estado'] = $request->boolean('estado') ? 'activo' : 'inactivo';

    if ($request->hasFile('imagen')) {
        $imagenAnterior = $servicio->imagen;

        $validated['imagen'] = $this->guardarImagen($request, 'imagen', 'services', 'admin-service');

        $servicio->update($validated);

        $this->eliminarImagenPublica($imagenAnterior, 'images/services/');
    } else {
        $servicio->update($validated);
    }

    return redirect()
        ->route('admin.dashboard', ['tab' => 'servicios'])
        ->with('success', 'Servicio actualizado correctamente.');
}
    public function cambiarEstadoServicio(Servicio $servicio): RedirectResponse
    {
        $servicio->update([
            'estado' => $servicio->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        $mensaje = $servicio->estado === 'activo'
            ? 'Servicio activado y visible para clientes.'
            : 'Servicio desactivado y oculto para clientes.';

        return redirect()
            ->route('admin.dashboard', ['tab' => 'servicios'])
            ->with('success', $mensaje);
    }

    public function cambiarEstadoTrabajador(Trabajador $trabajador): RedirectResponse
{
    $trabajador->update([
        'estado' => $trabajador->estado === 'activo' ? 'inactivo' : 'activo',
    ]);

    $mensaje = $trabajador->estado === 'activo'
        ? 'Trabajador activado y disponible para nuevas citas.'
        : 'Trabajador desactivado. Ya no aparecerá disponible para nuevas reservas.';

    return redirect()
        ->route('admin.dashboard', ['tab' => 'personal'])
        ->with('success', $mensaje);
}

public function eliminarServicio(Servicio $servicio): RedirectResponse
{
    $tieneCitas = $servicio->citas()->exists() || $servicio->detalles()->exists();

    if ($tieneCitas) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'servicios'])
            ->with('error', 'No se puede eliminar este servicio porque tiene citas registradas. Desactiva el servicio para que no aparezca al cliente y asegurate que no está siendo utilizado en ninguna reserva.');
    }

    try {
        $imagen = $servicio->imagen;

        $servicio->trabajadores()->detach();
        $servicio->delete();

        $this->eliminarImagenPublica($imagen, 'images/services/');

        return redirect()
            ->route('admin.dashboard', ['tab' => 'servicios'])
            ->with('success', 'Servicio eliminado correctamente.');
    } catch (QueryException $exception) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'servicios'])
            ->with('error', 'No se pudo eliminar el servicio porque está relacionado con otros registros. Puedes desactivarlo para ocultarlo al cliente.');
    }
}

    public function crearSeccionPersonal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_servicio' => ['required', 'string', 'max:100', 'unique:servicios,nombre_servicio'],
        ], [], [
            'nombre_servicio' => 'nombre de la sección',
        ]);

        Servicio::create([
            'nombre_servicio' => $validated['nombre_servicio'],
            'descripcion' => 'Sección administrativa para agrupar trabajadores. Edita esta descripción desde Gestión de servicios si deseas mostrarla al cliente.',
            'precio' => 0,
            'duracion_minutos' => 30,
            'estado' => 'inactivo',
            'imagen' => 'default-service.jpg',
        ]);

        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('success', 'Sección creada correctamente.');
    }

    public function actualizarSeccionPersonal(Request $request, Servicio $servicio): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_servicio' => ['required', 'string', 'max:100'],
        ], [], [
            'nombre_servicio' => 'nombre de la sección',
        ]);

        $servicio->update([
            'nombre_servicio' => $validated['nombre_servicio'],
        ]);

        foreach ($servicio->trabajadores as $trabajador) {
            $trabajador->update([
                'especialidad' => $validated['nombre_servicio'],
            ]);
        }

        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('success', 'Sección actualizada correctamente.');
    }

    public function guardarTrabajador(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->reglasTrabajador(true), [], $this->atributosTrabajador());

        $servicio = Servicio::findOrFail($validated['id_servicio']);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->guardarImagen($request, 'foto', 'services', 'admin-worker');
        }

        $trabajador = Trabajador::create([
            'nombre_completo' => $validated['nombre_completo'],
            'especialidad' => $servicio->nombre_servicio,
            'foto' => $validated['foto'] ?? 'default-service.jpg',
            'estado' => 'activo',
        ]);

        $trabajador->servicios()->sync([$servicio->id_servicio]);

        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('success', 'Trabajador agregado correctamente.');
    }

 public function actualizarTrabajador(Request $request, Trabajador $trabajador): RedirectResponse
{
    $validated = $request->validate($this->reglasTrabajador(false), [], $this->atributosTrabajador());

    $servicio = Servicio::findOrFail($validated['id_servicio']);

    $data = [
        'nombre_completo' => $validated['nombre_completo'],
        'especialidad' => $servicio->nombre_servicio,
    ];

    if ($request->hasFile('foto')) {
        $fotoAnterior = $trabajador->foto;

        $data['foto'] = $this->guardarImagen($request, 'foto', 'services', 'admin-worker');

        $trabajador->update($data);

        $this->eliminarImagenPublica($fotoAnterior, 'images/services/');
    } else {
        $trabajador->update($data);
    }

    $trabajador->servicios()->sync([$servicio->id_servicio]);

    return redirect()
        ->route('admin.dashboard', ['tab' => 'personal'])
        ->with('success', 'Trabajador actualizado correctamente.');
}

public function eliminarTrabajador(Trabajador $trabajador): RedirectResponse
{
    $tieneCitas = $trabajador->citas()->exists() || $trabajador->detalles()->exists();

    if ($tieneCitas) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se puede eliminar este trabajador porque tiene citas registradas. Desactívalo para que no aparezca en nuevas reservas y asegurate que no está siendo utilizado en ninguna reserva.');
    }

    try {
        $foto = $trabajador->foto;

        $trabajador->servicios()->detach();
        $trabajador->delete();

        $this->eliminarImagenPublica($foto, 'images/services/');

        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('success', 'Trabajador eliminado correctamente.');
    } catch (QueryException $exception) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se pudo eliminar el trabajador porque está relacionado con otros registros. Puedes desactivarlo para ocultarlo de nuevas reservas.');
    }
}
public function eliminarSeccionPersonal(Servicio $servicio): RedirectResponse
{
    $tieneCitas = $servicio->citas()->exists() || $servicio->detalles()->exists();

    if ($tieneCitas) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se puede eliminar esta sección porque tiene servicios o citas registradas. Puedes desactivarla desde Gestión de servicios.');
    }

    try {
        $servicio->trabajadores()->detach();

        $imagen = $servicio->imagen;
        $servicio->delete();

        $this->eliminarImagenPublica($imagen, 'images/services/');

        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('success', 'Sección eliminada correctamente.');
    } catch (QueryException $exception) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se pudo eliminar la sección porque tiene registros asociados.');
    }
}
    public function actualizarAsistencia(Request $request, Cita $cita): RedirectResponse
    {
        $validated = $request->validate([
            'estado' => ['required', 'in:completada,inasistencia'],
        ]);

        if ($cita->estado === 'cancelada') {
            return back()->with('error', 'No se puede registrar asistencia en una cita cancelada.');
        }

        if (in_array($cita->estado, ['completada', 'inasistencia'], true)) {
            return back()->with('error', 'Esta cita ya tiene asistencia registrada.');
        }

        $this->garantizarEstadosCitasHu07();

        $cita->update([
            'estado' => $validated['estado'],
        ]);

        $mensaje = $validated['estado'] === 'completada'
            ? 'La cita fue marcada como asistida correctamente.'
            : 'La cita fue marcada como no asistida correctamente.';

        return back()->with('success', $mensaje);
    }

    private function configuracionSitio(): ConfiguracionSitio
    {
        $this->garantizarTablaConfiguracionSitio();

        return ConfiguracionSitio::firstOrCreate([], [
            'hero_imagen' => 'default-service.jpg',
            'contacto_ubicacion' => 'Pasto, Nariño',
            'contacto_telefono' => '7291317',
            'contacto_correo' => 'marly@centrobelleza.com',
            'contacto_horario' => 'lunes a viernes de 7:00 a.m. a 7:00 p.m. y sábados y festivos de 8:00 a.m. a 7:00 p.m.',
            'instagram_url' => 'https://www.instagram.com/marly.salon?igsh=eGhtNTZscnZ1cnR3',
            'whatsapp_url' => 'https://wa.link/rsduzp',
        ]);
    }

    private function garantizarTablaConfiguracionSitio(): void
    {
        if (Schema::hasTable('configuracion_sitio')) {
            return;
        }

        Schema::create('configuracion_sitio', function ($table) {
            $table->id();
            $table->string('hero_imagen')->nullable();
            $table->string('contacto_ubicacion')->default('Pasto, Nariño');
            $table->string('contacto_telefono', 30)->default('7291317');
            $table->string('contacto_correo')->default('marly@centrobelleza.com');
            $table->text('contacto_horario')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('whatsapp_url')->nullable();
            $table->timestamps();
        });
    }

private function guardarImagen(Request $request, string $campo, string $carpeta, string $prefijo): string
{
    if (! $request->hasFile($campo)) {
        return '';
    }

    $archivo = $request->file($campo);

    $cloudName = config('services.cloudinary.cloud_name');
    $apiKey = config('services.cloudinary.api_key');
    $apiSecret = config('services.cloudinary.api_secret');

    if (! $cloudName || ! $apiKey || ! $apiSecret) {
        throw new \RuntimeException('Cloudinary no está configurado correctamente.');
    }

    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => $cloudName,
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
        ],
        'url' => [
            'secure' => true,
        ],
    ]);

    $nombreArchivo = $prefijo . '-' . uniqid();

    $resultado = $cloudinary->uploadApi()->upload(
        $archivo->getRealPath(),
        [
            'folder' => 'marly-centro-belleza/' . $carpeta,
            'public_id' => $nombreArchivo,
            'overwrite' => false,
        ]
    );

    return $resultado['secure_url'];
}
    private function medidasImagenPorPrefijo(string $prefijo): array
    {
        return match ($prefijo) {
            'admin-service' => [
                'ancho' => 1200,
                'alto' => 800,
            ],
            'admin-worker' => [
                'ancho' => 900,
                'alto' => 900,
            ],
            'hero-admin' => [
                'ancho' => 1600,
                'alto' => 900,
            ],
            default => [
                'ancho' => 1200,
                'alto' => 800,
            ],
        };
    }

private function procesarImagenProfesional(UploadedFile $archivo, string $rutaDestino, int $anchoDestino, int $altoDestino): bool
{
    if (! extension_loaded('gd')) {
        return false;
    }

    $rutaOrigen = $archivo->getRealPath();

    if (! $rutaOrigen || ! file_exists($rutaOrigen)) {
        return false;
    }

    $info = @getimagesize($rutaOrigen);

    if (! $info || empty($info['mime'])) {
        return false;
    }

    $imagenOriginal = false;

    if ($info['mime'] === 'image/jpeg' && function_exists('imagecreatefromjpeg')) {
        $imagenOriginal = @imagecreatefromjpeg($rutaOrigen);
    }

    if ($info['mime'] === 'image/png' && function_exists('imagecreatefrompng')) {
        $imagenOriginal = @imagecreatefrompng($rutaOrigen);
    }

    if ($info['mime'] === 'image/webp' && function_exists('imagecreatefromwebp')) {
        $imagenOriginal = @imagecreatefromwebp($rutaOrigen);
    }

    if (! $imagenOriginal) {
        return false;
    }

    $anchoOriginal = imagesx($imagenOriginal);
    $altoOriginal = imagesy($imagenOriginal);

    if ($anchoOriginal <= 0 || $altoOriginal <= 0) {
        imagedestroy($imagenOriginal);
        return false;
    }

    $escala = max($anchoDestino / $anchoOriginal, $altoDestino / $altoOriginal);

    $nuevoAncho = (int) ceil($anchoOriginal * $escala);
    $nuevoAlto = (int) ceil($altoOriginal * $escala);

    $posicionX = (int) (($anchoDestino - $nuevoAncho) / 2);
    $posicionY = (int) (($altoDestino - $nuevoAlto) / 2);

    $canvas = imagecreatetruecolor($anchoDestino, $altoDestino);

    $fondo = imagecolorallocate($canvas, 245, 241, 233);
    imagefill($canvas, 0, 0, $fondo);

    imagecopyresampled(
        $canvas,
        $imagenOriginal,
        $posicionX,
        $posicionY,
        0,
        0,
        $nuevoAncho,
        $nuevoAlto,
        $anchoOriginal,
        $altoOriginal
    );

    $guardado = imagejpeg($canvas, $rutaDestino, 88);

    imagedestroy($imagenOriginal);
    imagedestroy($canvas);

    return $guardado;
}

private function eliminarImagenPublica(?string $archivo, string $carpetaRelativa): void
{
    if (! $archivo) {
        return;
    }

    if (str_starts_with($archivo, 'http')) {
        return;
    }

    $imagenesProtegidas = [
        'default-service.jpg',
        'manicure.jpg',
        'pedicure.jpg',
        'peinado.jpg',
        'tinte.jpg',
        'maquillaje.jpg',
        'depilacion.jpg',
        'default-worker.jpg',
        'trabajador-default.jpg',
        'estilista-default.jpg',
    ];

    if (in_array($archivo, $imagenesProtegidas, true)) {
        return;
    }

    $ruta = public_path($carpetaRelativa . $archivo);

    if (File::exists($ruta)) {
        File::delete($ruta);
    }
}
    private function reglasServicio(bool $crear): array
    {
        return [
            'nombre_servicio' => ['required', 'string', 'max:100'],
            'descripcion' => ['required', 'string', 'max:700'],
            'precio' => ['required', 'numeric', 'min:0'],
            'duracion_minutos' => ['required', 'integer', 'min:1', 'max:600'],
            'estado' => ['nullable'],
            'imagen' => [$crear ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    private function atributosServicio(): array
    {
        return [
            'nombre_servicio' => 'nombre del servicio',
            'descripcion' => 'descripción',
            'precio' => 'precio estimado',
            'duracion_minutos' => 'duración estimada',
            'imagen' => 'foto del servicio',
        ];
    }

    private function reglasTrabajador(bool $crear): array
    {
        return [
            'nombre_completo' => ['required', 'string', 'max:100'],
            'id_servicio' => ['required', 'exists:servicios,id_servicio'],
            'foto' => [$crear ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    private function atributosTrabajador(): array
    {
        return [
            'nombre_completo' => 'nombre del trabajador',
            'id_servicio' => 'sección o servicio',
            'foto' => 'foto del trabajador',
        ];
    }

    private function garantizarEstadosCitasHu07(): void
    {
        try {
            DB::statement("ALTER TABLE citas MODIFY estado ENUM('registrada','confirmada','cancelada','completada','inasistencia') NOT NULL DEFAULT 'registrada'");
        } catch (\Throwable $exception) {
        }
    }

    private function obtenerCitasPorFecha(Carbon $fecha, string $busqueda = ''): Collection
    {
        return $this->consultaCitas($busqueda)
            ->where('fecha_cita', $fecha->format('Y-m-d'))
            ->orderBy('hora_inicio')
            ->get();
    }

    private function consultaCitas(string $busqueda = '')
    {
        return Cita::with(['cliente', 'trabajador', 'servicios', 'detalles.trabajador', 'detalles.servicio'])
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $busquedaNormalizada = mb_strtolower($busqueda, 'UTF-8');
                $busquedaTelefono = preg_replace('/\D+/', '', $busqueda);

                $query->where(function ($subquery) use ($busquedaNormalizada, $busquedaTelefono) {
                    $subquery->whereRaw('LOWER(nombre_cliente) LIKE ?', ["%{$busquedaNormalizada}%"])
                        ->orWhereHas('cliente', fn ($clienteQuery) => $clienteQuery->whereRaw('LOWER(nombre_completo) LIKE ?', ["%{$busquedaNormalizada}%"]))
                        ->orWhereHas('servicios', fn ($servicioQuery) => $servicioQuery->whereRaw('LOWER(nombre_servicio) LIKE ?', ["%{$busquedaNormalizada}%"]));

                    if ($busquedaTelefono !== '') {
                        $subquery->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefono_contacto, ' ', ''), '-', ''), '.', ''), '+', '') LIKE ?", ["%{$busquedaTelefono}%"])
                            ->orWhereHas('cliente', fn ($clienteQuery) => $clienteQuery->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefono, ' ', ''), '-', ''), '.', ''), '+', '') LIKE ?", ["%{$busquedaTelefono}%"]));
                    }
                });
            });
    }

    private function agruparCitasPorTiempo(Collection $citas): Collection
    {
        $hoy = Carbon::today();

        $inicioSemana = $hoy->copy()->startOfWeek(Carbon::MONDAY);
        $finSemana = $hoy->copy()->endOfWeek(Carbon::SUNDAY);

        $inicioSemanaPasada = $inicioSemana->copy()->subWeek();
        $finSemanaPasada = $finSemana->copy()->subWeek();

        $inicioMesAnterior = $hoy->copy()->subMonthNoOverflow()->startOfMonth();
        $finMesAnterior = $hoy->copy()->subMonthNoOverflow()->endOfMonth();

        return $citas->groupBy(function (Cita $cita) use (
            $inicioSemana,
            $finSemana,
            $inicioSemanaPasada,
            $finSemanaPasada,
            $inicioMesAnterior,
            $finMesAnterior
        ) {
            $fecha = Carbon::parse($cita->fecha_cita);

            if ($fecha->isToday()) {
                return 'Citas de hoy';
            }

            if ($fecha->isFuture()) {
                return 'Próximas citas';
            }

            if ($fecha->betweenIncluded($inicioSemana, $finSemana)) {
                return 'Citas de esta semana';
            }

            if ($fecha->betweenIncluded($inicioSemanaPasada, $finSemanaPasada)) {
                return 'Citas de la semana pasada';
            }

            if ($fecha->betweenIncluded($inicioMesAnterior, $finMesAnterior)) {
                return 'Citas del anterior mes';
            }

            return 'Citas anteriores';
        });
    }
}