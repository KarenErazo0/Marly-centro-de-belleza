<?php

namespace App\Http\Controllers\Cliente\Citas;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajador;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ControladorCitasCliente extends Controller
{
    private const SESSION_KEY = 'reserva_cita';

    public function index(Request $request): View
    {
        Carbon::setLocale('es');
        $cliente = Cliente::with(['citas.trabajador', 'citas.servicios', 'citas.detalles.trabajador', 'citas.detalles.servicio'])
            ->findOrFail(session('cliente_id'));

        $busqueda = trim((string) $request->query('buscar', ''));

        $citas = $cliente->citas()
            ->with(['trabajador', 'servicios', 'detalles.trabajador', 'detalles.servicio'])
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $busquedaNormalizada = mb_strtolower($busqueda, 'UTF-8');
                $busquedaTelefono = preg_replace('/\D+/', '', $busqueda);

                $query->where(function ($subquery) use ($busquedaNormalizada, $busquedaTelefono) {
                    $subquery->whereRaw('LOWER(nombre_cliente) LIKE ?', ["%{$busquedaNormalizada}%"])
                        ->orWhereHas('servicios', function ($servicioQuery) use ($busquedaNormalizada) {
                            $servicioQuery->whereRaw('LOWER(nombre_servicio) LIKE ?', ["%{$busquedaNormalizada}%"]);
                        });

                    if ($busquedaTelefono !== '') {
                        $subquery->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefono_contacto, ' ', ''), '-', ''), '.', ''), '+', '') LIKE ?", ["%{$busquedaTelefono}%"]);
                    }
                });
            })
            ->orderByDesc('fecha_cita')
            ->orderByDesc('hora_inicio')
            ->get();

        return view('cliente.citas.index', [
            'cliente' => $cliente,
            'citas' => $citas,
            'busqueda' => $busqueda,
            'citasAgrupadas' => $this->agruparCitasPorTiempo($citas),
        ]);
    }

    public function seleccionarServicios(): RedirectResponse
    {
        return redirect()->to(route('inicio') . '#servicios');
    }

    public function guardarServicios(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'servicios' => ['required', 'array', 'min:1'],
            'servicios.*' => ['integer', 'exists:servicios,id_servicio'],
        ], [
            'servicios.required' => 'Debes seleccionar al menos un servicio.',
            'servicios.min' => 'Debes seleccionar al menos un servicio.',
        ]);

        $servicios = Servicio::whereIn('id_servicio', $validated['servicios'])->get();

        $reserva = $this->getReserva();
        $reserva['servicios'] = $servicios->pluck('id_servicio')->values()->all();
        $reserva['trabajador'] = null;
        $reserva['trabajadores'] = [];
        $reserva['fecha'] = null;
        $reserva['hora'] = null;
        $reserva['nombre_cliente'] = $reserva['nombre_cliente'] ?? session('cliente_nombre');
        $this->putReserva($reserva);

        return redirect()->route('cliente.citas.agendar.trabajador');
    }

    public function seleccionarTrabajador(): View|RedirectResponse
    {
        $servicios = $this->getServiciosSeleccionados();
        if ($servicios->isEmpty()) {
            return redirect()->to(route('inicio') . '#servicios')->with('error', 'Primero debes seleccionar uno o más servicios.');
        }

        $grupos = $this->getGruposProfesionales($servicios);

        return view('cliente.citas.trabajador', [
            'servicios' => $servicios,
            'grupos' => $grupos,
            'reserva' => $this->getReserva(),
        ]);
    }

    public function guardarTrabajador(Request $request): RedirectResponse
    {
        $servicios = $this->getServiciosSeleccionados();
        if ($servicios->isEmpty()) {
            return redirect()->to(route('inicio') . '#servicios')->with('error', 'Primero debes seleccionar uno o más servicios.');
        }

        $request->validate([
            'accion' => ['required', 'in:manual,aleatorio'],
            'trabajadores' => ['nullable', 'array'],
        ]);

        $grupos = $this->getGruposProfesionales($servicios);
        if ($grupos->isEmpty()) {
            return back()->with('error', 'No hay profesionales disponibles para los servicios seleccionados.');
        }

        $seleccionados = [];

        foreach ($grupos as $grupo) {
            if ($grupo['trabajadores']->isEmpty()) {
                return back()->with('error', 'No hay profesionales disponibles en el área de ' . strtolower($grupo['nombre']) . '.');
            }

            if ($request->accion === 'aleatorio') {
                $trabajador = $grupo['trabajadores']->random();
            } else {
                $idSeleccionado = (int) data_get($request->input('trabajadores', []), $grupo['clave']);
                $trabajador = $grupo['trabajadores']->firstWhere('id_trabajador', $idSeleccionado);

                if (! $trabajador) {
                    return back()->with('error', 'Debes seleccionar un profesional válido para el área de ' . strtolower($grupo['nombre']) . '.');
                }
            }

            $seleccionados[$grupo['clave']] = $trabajador->id_trabajador;
        }

        $reserva = $this->getReserva();
        $reserva['trabajadores'] = $seleccionados;
        $reserva['trabajador'] = reset($seleccionados) ?: null;
        $reserva['fecha'] = null;
        $reserva['hora'] = null;
        $this->putReserva($reserva);

        return redirect()->route('cliente.citas.agendar.horario')->with('success', 'Profesionales seleccionados correctamente.');
    }

    public function seleccionarHorario(Request $request): View|RedirectResponse
    {
        $servicios = $this->getServiciosSeleccionados();
        $trabajadores = $this->getTrabajadoresSeleccionados();

        if ($servicios->isEmpty()) {
            return redirect()->to(route('inicio') . '#servicios')->with('error', 'Primero debes seleccionar uno o más servicios.');
        }

        if ($trabajadores->isEmpty()) {
            return redirect()->route('cliente.citas.agendar.trabajador')->with('error', 'Primero debes seleccionar los profesionales requeridos.');
        }

        $diasDisponibles = $this->buildDiasDisponibles($trabajadores, $servicios, 21);
        $fechaSeleccionada = $request->query('fecha')
            ?? $this->getReserva()['fecha']
            ?? optional($diasDisponibles->first())['fecha'];
        $horasDisponibles = $fechaSeleccionada
            ? $this->buildHorasDisponibles($trabajadores, $servicios, $fechaSeleccionada)
            : collect();

        $reservaActual = $this->getReserva();
        if (($reservaActual['fecha'] ?? null) !== $fechaSeleccionada) {
            $reservaActual['hora'] = null;
        }

        return view('cliente.citas.horario', [
            'servicios' => $servicios,
            'trabajadores' => $trabajadores,
            'reserva' => $reservaActual,
            'diasDisponibles' => $diasDisponibles,
            'fechaSeleccionada' => $fechaSeleccionada,
            'horasDisponibles' => $horasDisponibles,
            'duracionTotal' => $servicios->sum('duracion_minutos'),
        ]);
    }

    public function guardarHorario(Request $request): RedirectResponse
    {
        $servicios = $this->getServiciosSeleccionados();
        $trabajadores = $this->getTrabajadoresSeleccionados();

        if ($servicios->isEmpty() || $trabajadores->isEmpty()) {
            return redirect()->to(route('inicio') . '#servicios')->with('error', 'Debes completar los pasos anteriores para continuar.');
        }

        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
        ], [
            'fecha.required' => 'Debes seleccionar una fecha.',
            'hora.required' => 'Debes seleccionar una hora.',
        ]);

        $horasDisponibles = $this->buildHorasDisponibles($trabajadores, $servicios, $validated['fecha']);
        if (! $horasDisponibles->contains($validated['hora'])) {
            return back()->withInput()->with('error', 'La hora seleccionada ya no está disponible.');
        }

        $reserva = $this->getReserva();
        $reserva['fecha'] = $validated['fecha'];
        $reserva['hora'] = $validated['hora'];
        $this->putReserva($reserva);

        return redirect()->route('cliente.citas.agendar.confirmacion');
    }

    public function confirmar(): View|RedirectResponse
    {
        $servicios = $this->getServiciosSeleccionados();
        $trabajadores = $this->getTrabajadoresSeleccionados();
        $reserva = $this->getReserva();

        if ($servicios->isEmpty() || $trabajadores->isEmpty() || empty($reserva['fecha']) || empty($reserva['hora'])) {
            return redirect()->to(route('inicio') . '#servicios')->with('error', 'Debes completar el proceso de reserva antes de confirmar.');
        }

        $cliente = Cliente::findOrFail(session('cliente_id'));

        return view('cliente.citas.confirmacion', [
            'cliente' => $cliente,
            'servicios' => $servicios,
            'trabajadores' => $trabajadores,
            'gruposSeleccionados' => $this->getGruposSeleccionados($servicios, $trabajadores),
            'reserva' => $reserva,
            'duracionTotal' => $servicios->sum('duracion_minutos'),
        ]);
    }

    public function registrar(Request $request): RedirectResponse
    {
        $servicios = $this->getServiciosSeleccionados();
        $trabajadores = $this->getTrabajadoresSeleccionados();
        $reserva = $this->getReserva();

        if ($servicios->isEmpty() || $trabajadores->isEmpty() || empty($reserva['fecha']) || empty($reserva['hora'])) {
            return redirect()->to(route('inicio') . '#servicios')->with('error', 'Debes completar el proceso de reserva antes de confirmar.');
        }

        $validated = $request->validate([
            'nombre_cliente' => ['required', 'string', 'max:100'],
            'telefono_contacto' => ['required', 'string', 'max:20'],
            'notas' => ['nullable', 'string', 'max:500'],
        ], [
            'nombre_cliente.required' => 'El nombre completo es obligatorio.',
            'telefono_contacto.required' => 'El teléfono es obligatorio.',
        ]);

        $horasDisponibles = $this->buildHorasDisponibles($trabajadores, $servicios, $reserva['fecha']);
        if (! $horasDisponibles->contains($reserva['hora'])) {
            return redirect()->route('cliente.citas.agendar.horario')->with('error', 'La disponibilidad cambió. Selecciona otro horario.');
        }

        $duracionTotal = (int) $servicios->sum('duracion_minutos');
        $inicio = Carbon::createFromFormat('Y-m-d H:i', $reserva['fecha'] . ' ' . $reserva['hora']);
        $fin = $inicio->copy()->addMinutes($duracionTotal);
        $gruposSeleccionados = $this->getGruposSeleccionados($servicios, $trabajadores);
        $trabajadorPrincipal = $trabajadores->first();

        DB::transaction(function () use ($validated, $trabajadorPrincipal, $servicios, $duracionTotal, $inicio, $fin, $gruposSeleccionados) {
            $cita = Cita::create([
                'id_cliente' => session('cliente_id'),
                'id_trabajador' => $trabajadorPrincipal->id_trabajador,
                'nombre_cliente' => $validated['nombre_cliente'],
                'telefono_contacto' => $validated['telefono_contacto'],
                'fecha_cita' => $inicio->format('Y-m-d'),
                'hora_inicio' => $inicio->format('H:i:s'),
                'hora_fin' => $fin->format('H:i:s'),
                'duracion_total_minutos' => $duracionTotal,
                'notas' => $validated['notas'] ?? null,
                'estado' => 'registrada',
                'fecha_registro' => now(),
            ]);

            $cita->servicios()->sync($servicios->pluck('id_servicio')->all());

            foreach ($gruposSeleccionados as $grupo) {
                foreach ($grupo['servicios'] as $servicio) {
                    DB::table('cita_detalle')->insert([
                        'id_cita' => $cita->id_cita,
                        'id_servicio' => $servicio->id_servicio,
                        'id_trabajador' => $grupo['trabajador']->id_trabajador,
                        'area' => $grupo['nombre'],
                    ]);
                }
            }
        });

        session()->forget(self::SESSION_KEY);

        return redirect()->route('cliente.citas.index')->with('success', 'Tu cita fue registrada correctamente.');
    }

    public function editar(Request $request, Cita $cita): View|RedirectResponse
    {
        Carbon::setLocale('es');
        if ((int) $cita->id_cliente !== (int) session('cliente_id')) {
            abort(403);
        }

        if (! $this->citaPuedeModificarse($cita)) {
            return redirect()->route('cliente.citas.index')->with('error', 'Esta cita ya no se puede modificar.');
        }

        $cita->load(['trabajador', 'servicios', 'detalles.trabajador', 'detalles.servicio']);
        $trabajadores = $cita->detalles->pluck('trabajador')->filter()->unique('id_trabajador')->values();
        if ($trabajadores->isEmpty() && $cita->trabajador) {
            $trabajadores = collect([$cita->trabajador]);
        }

        $fechaSeleccionada = $request->query('fecha', $cita->fecha_cita);
        $horasDisponibles = $this->buildHorasDisponibles($trabajadores, $cita->servicios, $fechaSeleccionada, $cita->id_cita);

        return view('cliente.citas.editar', [
            'cita' => $cita,
            'fechaSeleccionada' => $fechaSeleccionada,
            'horasDisponibles' => $horasDisponibles,
        ]);
    }

    public function actualizar(Request $request, Cita $cita): RedirectResponse
    {
        if ((int) $cita->id_cliente !== (int) session('cliente_id')) {
            abort(403);
        }

        if (! $this->citaPuedeModificarse($cita)) {
            return redirect()->route('cliente.citas.index')->with('error', 'Esta cita ya no se puede modificar.');
        }

        $validated = $request->validate([
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
            'telefono_contacto' => ['required', 'string', 'max:20'],
            'notas' => ['nullable', 'string', 'max:500'],
        ], [
            'fecha.required' => 'Debes seleccionar una fecha.',
            'fecha.after_or_equal' => 'La fecha de la cita no puede ser anterior a hoy.',
            'hora.required' => 'Debes seleccionar una hora disponible.',
            'telefono_contacto.required' => 'El teléfono es obligatorio.',
        ]);

        $cita->load(['trabajador', 'servicios', 'detalles.trabajador']);
        $trabajadores = $cita->detalles->pluck('trabajador')->filter()->unique('id_trabajador')->values();
        if ($trabajadores->isEmpty() && $cita->trabajador) {
            $trabajadores = collect([$cita->trabajador]);
        }

        $horasDisponibles = $this->buildHorasDisponibles($trabajadores, $cita->servicios, $validated['fecha'], $cita->id_cita);
        if (! $horasDisponibles->contains($validated['hora'])) {
            return back()->withInput()->with('error', 'La hora seleccionada no está disponible.');
        }

        $inicio = Carbon::createFromFormat('Y-m-d H:i', $validated['fecha'] . ' ' . $validated['hora']);
        $fin = $inicio->copy()->addMinutes((int) $cita->duracion_total_minutos);

        $cita->update([
            'fecha_cita' => $inicio->format('Y-m-d'),
            'hora_inicio' => $inicio->format('H:i:s'),
            'hora_fin' => $fin->format('H:i:s'),
            'telefono_contacto' => $validated['telefono_contacto'],
            'notas' => $validated['notas'] ?? null,
            'estado' => 'registrada',
        ]);

        return redirect()->route('cliente.citas.index')->with('success', 'Tu cita fue modificada correctamente.');
    }

    public function cancelar(Cita $cita): RedirectResponse
    {
        if ((int) $cita->id_cliente !== (int) session('cliente_id')) {
            abort(403);
        }

        if (! $this->citaPuedeModificarse($cita)) {
            return redirect()->route('cliente.citas.index')->with('error', 'Esta cita ya no se puede cancelar.');
        }

        $cita->update(['estado' => 'cancelada']);

        return redirect()->route('cliente.citas.index')->with('success', 'Tu cita fue cancelada correctamente.');
    }

    private function citaPuedeModificarse(Cita $cita): bool
    {
        if (in_array($cita->estado, ['cancelada', 'completada', 'inasistencia'], true)) {
            return false;
        }

        $inicio = Carbon::createFromFormat('Y-m-d H:i:s', $cita->fecha_cita . ' ' . $cita->hora_inicio);

        return $inicio->isFuture();
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

        $orden = [
            'Proximas citas' => 1,
            'Citas de esta semana' => 2,
            'Citas de la semana pasada' => 3,
            'Citas del anterior mes' => 4,
            'Citas anteriores' => 5,
        ];

        return $citas->groupBy(function (Cita $cita) use ($hoy, $inicioSemana, $finSemana, $inicioSemanaPasada, $finSemanaPasada, $inicioMesAnterior, $finMesAnterior) {
            $fecha = Carbon::parse($cita->fecha_cita);

            if ($fecha->betweenIncluded($inicioSemana, $finSemana)) {
                return 'Citas de esta semana';
            }

            if ($fecha->gt($finSemana)) {
                return 'Proximas citas';
            }

            if ($fecha->betweenIncluded($inicioSemanaPasada, $finSemanaPasada)) {
                return 'Citas de la semana pasada';
            }

            if ($fecha->betweenIncluded($inicioMesAnterior, $finMesAnterior)) {
                return 'Citas del anterior mes';
            }

            return 'Citas anteriores';
        })->sortBy(fn ($grupo, $nombre) => $orden[$nombre] ?? 99);
    }

    private function getReserva(): array
    {
        return session(self::SESSION_KEY, []);
    }

    private function putReserva(array $data): void
    {
        session([self::SESSION_KEY => $data]);
    }

    private function getServiciosSeleccionados(): Collection
    {
        $ids = collect($this->getReserva()['servicios'] ?? [])->filter()->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Servicio::whereIn('id_servicio', $ids)->orderBy('nombre_servicio')->get();
    }

    private function getTrabajadoresSeleccionados(): Collection
    {
        $ids = collect($this->getReserva()['trabajadores'] ?? [])->filter()->unique()->values();

        if ($ids->isEmpty()) {
            $id = $this->getReserva()['trabajador'] ?? null;
            $ids = collect($id ? [$id] : []);
        }

        if ($ids->isEmpty()) {
            return collect();
        }

        return Trabajador::whereIn('id_trabajador', $ids)
            ->orderByDesc('calificacion')
            ->orderBy('nombre_completo')
            ->get();
    }

    private function getGruposProfesionales(Collection $servicios): Collection
    {
        return $this->agruparServiciosPorArea($servicios)->map(function (Collection $serviciosGrupo, string $area) {
            $ids = $serviciosGrupo->pluck('id_servicio')->all();
            $trabajadores = Trabajador::with('servicios')
                ->where('estado', 'activo')
                ->whereHas('servicios', function ($query) use ($ids) {
                    $query->whereIn('servicios.id_servicio', $ids);
                })
                ->orderByDesc('calificacion')
                ->orderBy('nombre_completo')
                ->get()
                ->unique('id_trabajador')
                ->values();

            return [
                'clave' => $area,
                'nombre' => $this->labelArea($area),
                'servicios' => $serviciosGrupo->values(),
                'trabajadores' => $trabajadores,
            ];
        })->values();
    }

    private function getGruposSeleccionados(Collection $servicios, Collection $trabajadoresSeleccionados): Collection
    {
        $porId = $trabajadoresSeleccionados->keyBy('id_trabajador');
        $seleccionados = collect($this->getReserva()['trabajadores'] ?? []);

        return $this->getGruposProfesionales($servicios)->map(function (array $grupo) use ($seleccionados, $porId) {
            $idTrabajador = (int) $seleccionados->get($grupo['clave']);
            return [
                'clave' => $grupo['clave'],
                'nombre' => $grupo['nombre'],
                'servicios' => $grupo['servicios'],
                'trabajador' => $porId->get($idTrabajador),
            ];
        })->filter(fn (array $grupo) => $grupo['trabajador'] !== null)->values();
    }

    private function agruparServiciosPorArea(Collection $servicios): Collection
    {
        return $servicios->groupBy(fn (Servicio $servicio) => $this->resolverAreaServicio($servicio));
    }

    private function resolverAreaServicio(Servicio $servicio): string
    {
        $texto = mb_strtolower($servicio->nombre_servicio . ' ' . ($servicio->descripcion ?? ''));

        if (str_contains($texto, 'depil')) {
            return 'depilacion';
        }

        if (str_contains($texto, 'manicure') || str_contains($texto, 'pedicure') || str_contains($texto, 'uña') || str_contains($texto, 'unas')) {
            return 'unas';
        }

        if (str_contains($texto, 'peinado') || str_contains($texto, 'tinte') || str_contains($texto, 'cabello') || str_contains($texto, 'capilar')) {
            return 'cabello';
        }

        if (str_contains($texto, 'maquill')) {
            return 'maquillaje';
        }

        return 'servicio_' . $servicio->id_servicio;
    }

    private function labelArea(string $area): string
    {
        return match ($area) {
            'depilacion' => 'Depilación',
            'unas' => 'Uñas',
            'cabello' => 'Cabello',
            'maquillaje' => 'Maquillaje',
            default => 'Servicio',
        };
    }

    private function buildDiasDisponibles(Collection $trabajadores, Collection $servicios, int $cantidadDias): Collection
    {
        $dias = collect();
        $fecha = Carbon::today();

        while ($dias->count() < $cantidadDias) {
            if (! $fecha->isSunday()) {
                $horas = $this->buildHorasDisponibles($trabajadores, $servicios, $fecha->format('Y-m-d'));
                if ($horas->isNotEmpty()) {
                    $dias->push([
                        'fecha' => $fecha->format('Y-m-d'),
                        'dia_semana' => ucfirst($fecha->translatedFormat('D')),
                        'dia' => $fecha->format('j'),
                        'mes' => ucfirst($fecha->translatedFormat('M')),
                        'cantidad_horas' => $horas->count(),
                    ]);
                }
            }
            $fecha->addDay();
        }

        return $dias;
    }

    private function buildHorasDisponibles(Collection $trabajadores, Collection $servicios, string $fecha, ?int $ignorarCitaId = null): Collection
    {
        $fechaCarbon = Carbon::parse($fecha);
        if ($fechaCarbon->isSunday()) {
            return collect();
        }

        $duracionTotal = (int) $servicios->sum('duracion_minutos');
        $apertura = $fechaCarbon->copy()->setTime($fechaCarbon->isSaturday() ? 8 : 7, 0);
        $cierre = $fechaCarbon->copy()->setTime(19, 0);

        $citasPorTrabajador = Cita::whereIn('id_trabajador', $trabajadores->pluck('id_trabajador')->unique()->all())
            ->where('fecha_cita', $fechaCarbon->format('Y-m-d'))
            ->whereIn('estado', ['registrada', 'confirmada'])
            ->when($ignorarCitaId, fn ($query) => $query->where('id_cita', '!=', $ignorarCitaId))
            ->orderBy('hora_inicio')
            ->get(['id_trabajador', 'hora_inicio', 'hora_fin'])
            ->groupBy('id_trabajador');

        $horas = collect();
        $slot = $apertura->copy();

        while ($slot->copy()->addMinutes($duracionTotal)->lte($cierre)) {
            $finSlot = $slot->copy()->addMinutes($duracionTotal);
            $ocupado = $trabajadores->contains(function (Trabajador $trabajador) use ($citasPorTrabajador, $fechaCarbon, $slot, $finSlot) {
                $citas = $citasPorTrabajador->get($trabajador->id_trabajador, collect());

                return $citas->contains(function ($cita) use ($fechaCarbon, $slot, $finSlot) {
                    $inicioCita = Carbon::createFromFormat('Y-m-d H:i:s', $fechaCarbon->format('Y-m-d') . ' ' . $cita->hora_inicio);
                    $finCita = Carbon::createFromFormat('Y-m-d H:i:s', $fechaCarbon->format('Y-m-d') . ' ' . $cita->hora_fin);

                    return $slot->lt($finCita) && $finSlot->gt($inicioCita);
                });
            });

            if (! $ocupado) {
                $horas->push($slot->format('H:i'));
            }

            $slot->addMinutes(60);
        }

        return $horas;
    }
}
