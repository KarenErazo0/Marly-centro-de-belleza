<?php

namespace App\Http\Controllers\Inicio;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function index(): View
    {
        $servicios = Servicio::where('estado', 'activo')
            ->orderBy('nombre_servicio')
            ->get();

        return view('inicio.index', [
            'servicios' => $servicios,
            'reserva' => session('reserva_cita', []),
        ]);
    }
}
