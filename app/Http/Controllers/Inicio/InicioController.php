<?php

namespace App\Http\Controllers\Inicio;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionSitio;
use App\Models\Servicio;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function index()
    {
        if (session('admin_autenticado')) {
            return redirect()->route('admin.dashboard');
        }

        $servicios = Servicio::where('estado', 'activo')
            ->orderBy('nombre_servicio')
            ->get();

        $valoresPorDefecto = [
            'hero_imagen' => 'default-service.jpg',
            'contacto_ubicacion' => 'Pasto, Nariño',
            'contacto_telefono' => '7291317',
            'contacto_correo' => 'marly@centrobelleza.com',
            'contacto_horario' => 'lunes a viernes de 7:00 a.m. a 7:00 p.m. y sábados y festivos de 8:00 a.m. a 7:00 p.m.',
            'instagram_url' => 'https://www.instagram.com/marly.salon?igsh=eGhtNTZscnZ1cnR3',
            'whatsapp_url' => 'https://wa.link/rsduzp',
        ];

        $configuracion = Schema::hasTable('configuracion_sitio')
            ? ConfiguracionSitio::firstOrCreate([], $valoresPorDefecto)
            : new ConfiguracionSitio($valoresPorDefecto);

        return view('inicio.index', [
            'servicios' => $servicios,
            'reserva' => session('reserva_cita', []),
            'configuracion' => $configuracion,
        ]);
    }
}
