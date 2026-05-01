<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $citasPorDia = Cita::whereBetween('fecha_cita', [$inicioMes->format('Y-m-d'), $finMes->format('Y-m-d')])
            ->selectRaw('fecha_cita, COUNT(*) as total')
            ->groupBy('fecha_cita')
            ->pluck('total', 'fecha_cita');

        $citasFechaSeleccionada = $this->obtenerCitasPorFecha($fechaSeleccionada);
        $citasHoy = $this->obtenerCitasPorFecha($hoy);

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
        ]);
    }

    private function obtenerCitasPorFecha(Carbon $fecha)
    {
        return Cita::with(['cliente', 'trabajador', 'servicios', 'detalles.trabajador', 'detalles.servicio'])
            ->where('fecha_cita', $fecha->format('Y-m-d'))
            ->orderBy('hora_inicio')
            ->get();
    }
}
