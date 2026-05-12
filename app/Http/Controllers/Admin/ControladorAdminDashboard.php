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

        $hoy = Carbon::today('America/Bogota');
$fechaSeleccionada = $request->query('fecha')
    ? Carbon::parse($request->query('fecha'), 'America/Bogota')
    : $hoy->copy();
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
public function eliminarServicio(Servicio $servicio): RedirectResponse
{
    if ($this->servicioTieneCitasPendientes($servicio)) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'servicios'])
            ->with('error', 'No se puede eliminar este servicio porque tiene citas pendientes o futuras registradas. Puedes desactivarlo mientras se atienden esas citas.');
    }

    try {
        DB::transaction(function () use ($servicio) {
            $imagen = $servicio->imagen;

            $trabajadores = $servicio->trabajadores()->get();

            foreach ($trabajadores as $trabajador) {
                $trabajador->servicios()->detach($servicio->id_servicio);

                $trabajador->load('servicios');

                if ($trabajador->servicios->count() === 0) {
                    if ($this->trabajadorTieneCitasPendientes($trabajador)) {
                        $trabajador->update([
                            'estado' => 'inactivo',
                            'especialidad' => 'Sin sección asignada',
                        ]);

                        continue;
                    }

                    $foto = $trabajador->foto;
                    $trabajador->delete();
                    $this->eliminarImagenPublica($foto, 'images/services/');
                } else {
                    $this->actualizarEspecialidadTrabajador($trabajador);
                }
            }

            $this->eliminarRelacionesHistoricasServicio($servicio);

            $servicio->delete();

            $this->eliminarImagenPublica($imagen, 'images/services/');
        });

        return redirect()
            ->route('admin.dashboard', ['tab' => 'servicios'])
            ->with('success', 'Servicio eliminado correctamente. No existían citas pendientes asociadas.');
    } catch (QueryException $exception) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'servicios'])
            ->with('error', 'No se pudo eliminar el servicio porque todavía tiene registros protegidos.');
    }
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

public function actualizarTrabajador(Request $request, Servicio $servicio, Trabajador $trabajador): RedirectResponse
{
    $validated = $request->validate([
        'id_servicio' => ['required', 'exists:servicios,id_servicio'],
        'nombre_completo' => ['required', 'string', 'max:255'],
        'foto' => ['nullable', 'image', 'max:4096'],
    ], [], [
        'id_servicio' => 'sección',
        'nombre_completo' => 'nombre',
        'foto' => 'foto',
    ]);

    $fotoAnterior = $trabajador->foto;

    $datos = [
        'nombre_completo' => $validated['nombre_completo'],
    ];

    if ($request->hasFile('foto')) {
        $datos['foto'] = $this->guardarImagen($request, 'foto', 'services', 'admin-worker');
    }

    try {
        DB::transaction(function () use ($trabajador, $servicio, $validated, $datos) {
            $trabajador->update($datos);

            /*
             * Importante:
             * este método edita la tarjeta desde una sección específica.
             * Por eso solo mueve la relación de esa sección y conserva las demás.
             */
            $this->limpiarRelacionesDuplicadasTrabajador($trabajador);

            $servicioActualId = (int) $servicio->id_servicio;
            $nuevoServicioId = (int) $validated['id_servicio'];

            if ($nuevoServicioId !== $servicioActualId) {
                $trabajador->servicios()->detach($servicioActualId);

                $yaExisteEnNuevoServicio = $trabajador->servicios()
                    ->where('servicios.id_servicio', $nuevoServicioId)
                    ->exists();

                if (! $yaExisteEnNuevoServicio) {
                    $trabajador->servicios()->attach($nuevoServicioId);
                }
            }

            $this->limpiarRelacionesDuplicadasTrabajador($trabajador);
            $this->actualizarEspecialidadTrabajador($trabajador);
        });
    } catch (QueryException $exception) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se pudo actualizar el trabajador. Revisa que la sección seleccionada sea válida.');
    }

    if ($request->hasFile('foto') && $fotoAnterior && $fotoAnterior !== $trabajador->foto) {
        $this->eliminarImagenPublica($fotoAnterior, 'images/services/');
    }

    return redirect()
        ->route('admin.dashboard', ['tab' => 'personal'])
        ->with('success', 'Trabajador actualizado correctamente.');
}

public function duplicarTrabajadorEnServicio(Request $request, Trabajador $trabajador): RedirectResponse
{
    $validated = $request->validate([
        'id_servicio' => ['required', 'exists:servicios,id_servicio'],
    ], [], [
        'id_servicio' => 'servicio o sección',
    ]);

    $servicio = Servicio::findOrFail($validated['id_servicio']);

    $this->limpiarRelacionesDuplicadasTrabajador($trabajador);

    if ($trabajador->servicios()->where('servicios.id_servicio', $servicio->id_servicio)->exists()) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'Este trabajador ya pertenece a esa sección.');
    }

    $trabajador->servicios()->syncWithoutDetaching([$servicio->id_servicio]);

    $this->limpiarRelacionesDuplicadasTrabajador($trabajador);
    $this->actualizarEspecialidadTrabajador($trabajador);

    return redirect()
        ->route('admin.dashboard', ['tab' => 'personal'])
        ->with('success', 'Trabajador duplicado correctamente en la sección seleccionada.');
}
public function eliminarTrabajadorDeServicio(Servicio $servicio, Trabajador $trabajador): RedirectResponse
{
    if (! $trabajador->servicios()->where('servicios.id_servicio', $servicio->id_servicio)->exists()) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'Este trabajador no pertenece a la sección seleccionada.');
    }

    $totalServicios = $trabajador->servicios()->count();
    $esUltimaSeccion = $totalServicios <= 1;

    if ($esUltimaSeccion && $this->trabajadorTieneCitasPendientes($trabajador)) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se puede quitar este trabajador porque es su última sección y tiene citas pendientes o futuras registradas.');
    }

    try {
        DB::transaction(function () use ($servicio, $trabajador, $esUltimaSeccion) {
            $trabajador->servicios()->detach($servicio->id_servicio);

            if ($esUltimaSeccion) {
                $foto = $trabajador->foto;

                $trabajador->delete();

                $this->eliminarImagenPublica($foto, 'images/services/');

                return;
            }

            $this->actualizarEspecialidadTrabajador($trabajador);
        });

        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('success', 'Trabajador quitado de esta sección correctamente.');
    } catch (QueryException $exception) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se pudo quitar el trabajador de esta sección.');
    }
}
private function limpiarRelacionesDuplicadasTrabajador(Trabajador $trabajador): void
{
    $servicioIds = $trabajador->servicios()
        ->pluck('servicios.id_servicio')
        ->map(fn ($id) => (int) $id)
        ->unique()
        ->values()
        ->all();

    $trabajador->servicios()->sync($servicioIds);
    $trabajador->load('servicios');
}

private function actualizarEspecialidadTrabajador(Trabajador $trabajador): void
{
    $trabajador->load('servicios');

    $especialidad = $trabajador->servicios
        ->pluck('nombre_servicio')
        ->filter()
        ->implode(', ');

    $trabajador->update([
        'especialidad' => $especialidad ?: 'Sin sección asignada',
    ]);
}

public function eliminarTrabajador(Trabajador $trabajador): RedirectResponse
{
    if ($this->trabajadorTieneCitasPendientes($trabajador)) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se puede eliminar este trabajador porque tiene citas pendientes o futuras registradas.');
    }

    try {
        DB::transaction(function () use ($trabajador) {
            $foto = $trabajador->foto;

            $trabajador->servicios()->detach();
            $trabajador->delete();

            $this->eliminarImagenPublica($foto, 'images/services/');
        });

        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('success', 'Trabajador eliminado correctamente.');
    } catch (QueryException $exception) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se pudo eliminar el trabajador porque tiene registros protegidos.');
    }
}
public function eliminarSeccionPersonal(Servicio $servicio): RedirectResponse
{
    if ($this->servicioTieneCitasPendientes($servicio)) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se puede eliminar esta sección porque tiene citas pendientes o futuras registradas. Puedes desactivarla desde Gestión de servicios.');
    }

    try {
        DB::transaction(function () use ($servicio) {
            $trabajadores = $servicio->trabajadores()->get();

            foreach ($trabajadores as $trabajador) {
                $trabajador->servicios()->detach($servicio->id_servicio);

                $trabajador->load('servicios');

                if ($trabajador->servicios->count() === 0) {
                    if ($this->trabajadorTieneCitasPendientes($trabajador)) {
                        $trabajador->update([
                            'estado' => 'inactivo',
                            'especialidad' => 'Sin sección asignada',
                        ]);

                        continue;
                    }

                    $foto = $trabajador->foto;
                    $trabajador->delete();
                    $this->eliminarImagenPublica($foto, 'images/services/');
                } else {
                    $this->actualizarEspecialidadTrabajador($trabajador);
                }
            }

            $imagen = $servicio->imagen;

            $this->eliminarRelacionesHistoricasServicio($servicio);

            $servicio->delete();

            $this->eliminarImagenPublica($imagen, 'images/services/');
        });

        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('success', 'Sección eliminada correctamente. No existían citas pendientes asociadas.');
    } catch (QueryException $exception) {
        return redirect()
            ->route('admin.dashboard', ['tab' => 'personal'])
            ->with('error', 'No se pudo eliminar la sección porque todavía tiene registros protegidos.');
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
    $hoy = Carbon::today('America/Bogota');

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
            $fecha = Carbon::parse($cita->fecha_cita, 'America/Bogota');
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
    private function trabajadorTieneCitasPendientes(Trabajador $trabajador): bool
{
    $ahora = Carbon::now('America/Bogota');
    $hoy = $ahora->format('Y-m-d');
    $horaActual = $ahora->format('H:i:s');

    return Cita::query()
        ->where('id_trabajador', $trabajador->id_trabajador)
        ->whereIn('estado', ['registrada', 'confirmada'])
        ->where(function ($query) use ($hoy, $horaActual) {
            $query->where('fecha_cita', '>', $hoy)
                ->orWhere(function ($subQuery) use ($hoy, $horaActual) {
                    $subQuery->where('fecha_cita', $hoy)
                        ->where('hora_fin', '>=', $horaActual);
                });
        })
        ->exists();
}
private function servicioTieneCitasPendientes(Servicio $servicio): bool
{
    $ahora = Carbon::now('America/Bogota');
    $hoy = $ahora->format('Y-m-d');
    $horaActual = $ahora->format('H:i:s');

    return Cita::query()
        ->whereIn('estado', ['registrada', 'confirmada'])
        ->where(function ($query) use ($servicio) {
            $query->whereHas('servicios', function ($servicioQuery) use ($servicio) {
                $servicioQuery->where('servicios.id_servicio', $servicio->id_servicio);
            })
            ->orWhereHas('detalles', function ($detalleQuery) use ($servicio) {
                $detalleQuery->where('id_servicio', $servicio->id_servicio);
            });
        })
        ->where(function ($query) use ($hoy, $horaActual) {
            $query->where('fecha_cita', '>', $hoy)
                ->orWhere(function ($subQuery) use ($hoy, $horaActual) {
                    $subQuery->where('fecha_cita', $hoy)
                        ->where('hora_fin', '>=', $horaActual);
                });
        })
        ->exists();
}

private function eliminarRelacionesHistoricasServicio(Servicio $servicio): void
{
    DB::table('cita_detalle')
        ->where('id_servicio', $servicio->id_servicio)
        ->delete();

    DB::table('cita_servicio')
        ->where('id_servicio', $servicio->id_servicio)
        ->delete();
}

}