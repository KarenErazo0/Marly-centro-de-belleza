<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ControladorAdminDashboard extends Controller
{
    public function index(Request $request): View
    {
        Carbon::setLocale('es');
        $hoy = Carbon::today();
        $fechaSeleccionada = $request->query('fecha')
            ? Carbon::parse($request->query('fecha'))
            : $hoy->copy();

        $mesActual = $request->query('mes')
            ? Carbon::createFromFormat('Y-m', $request->query('mes'))->startOfMonth()
            : $fechaSeleccionada->copy()->startOfMonth();

        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();
        $busqueda = trim((string) $request->query('buscar', ''));

        $citasPorDia = Cita::whereBetween('fecha_cita', [$inicioMes->format('Y-m-d'), $finMes->format('Y-m-d')])
            ->selectRaw('fecha_cita, COUNT(*) as total')
            ->groupBy('fecha_cita')
            ->pluck('total', 'fecha_cita');

        $citasFechaSeleccionada = $this->obtenerCitasPorFecha($fechaSeleccionada, $busqueda);
        $citasHoy = $this->obtenerCitasPorFecha($hoy, $busqueda);
        $todasCitas = $this->consultaCitas($busqueda)
            ->orderByDesc('fecha_cita')
            ->orderByDesc('hora_inicio')
            ->get();

        return view('admin.dashboard', [
            'hoy' => $hoy,
            'fechaSeleccionada' => $fechaSeleccionada,
            'mesActual' => $mesActual,
            'mesAnterior' => $mesActual->copy()->subMonth()->format('Y-m'),
            'mesSiguiente' => $mesActual->copy()->addMonth()->format('Y-m'),
            'inicioCalendario' => $inicioMes->copy()->startOfWeek(Carbon::MONDAY),
            'finCalendario' => $finMes->copy()->endOfWeek(Carbon::SUNDAY),
            'citasPorDia' => $citasPorDia,
            'citasFechaSeleccionada' => $citasFechaSeleccionada,
            'citasHoy' => $citasHoy,
            'todasCitas' => $todasCitas,
            'busqueda' => $busqueda,
            'citasAgrupadas' => $this->agruparCitasPorTiempo($todasCitas),
        ]);
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

        $cita->update(['estado' => $validated['estado']]);

        $mensaje = $validated['estado'] === 'completada'
            ? 'La cita fue marcada como asistida correctamente.'
            : 'La cita fue marcada como no asistida correctamente.';

        return back()->with('success', $mensaje);
    }

    private function garantizarEstadosCitasHu07(): void
    {
        try {
            DB::statement("ALTER TABLE citas MODIFY estado ENUM('registrada','confirmada','cancelada','completada','inasistencia') NOT NULL DEFAULT 'registrada'");
        } catch (\Throwable $exception) {
            // Si la columna ya está actualizada o la base no permite el ALTER en este momento,
            // se continúa para no interrumpir el flujo normal de la aplicación.
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
                        ->orWhereHas('cliente', function ($clienteQuery) use ($busquedaNormalizada) {
                            $clienteQuery->whereRaw('LOWER(nombre_completo) LIKE ?', ["%{$busquedaNormalizada}%"]);
                        })
                        ->orWhereHas('servicios', function ($servicioQuery) use ($busquedaNormalizada) {
                            $servicioQuery->whereRaw('LOWER(nombre_servicio) LIKE ?', ["%{$busquedaNormalizada}%"]);
                        });

                    if ($busquedaTelefono !== '') {
                        $subquery->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefono_contacto, ' ', ''), '-', ''), '.', ''), '+', '') LIKE ?", ["%{$busquedaTelefono}%"])
                            ->orWhereHas('cliente', function ($clienteQuery) use ($busquedaTelefono) {
                                $clienteQuery->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(telefono, ' ', ''), '-', ''), '.', ''), '+', '') LIKE ?", ["%{$busquedaTelefono}%"]);
                            });
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

        $orden = [
            'Proximas citas' => 1,
            'Citas de esta semana' => 2,
            'Citas de la semana pasada' => 3,
            'Citas del anterior mes' => 4,
            'Citas anteriores' => 5,
        ];

        return $citas->groupBy(function (Cita $cita) use ($inicioSemana, $finSemana, $inicioSemanaPasada, $finSemanaPasada, $inicioMesAnterior, $finMesAnterior) {
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
}
