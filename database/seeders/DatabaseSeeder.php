<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Servicio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['nombre_servicio' => 'Peinado social', 'descripcion' => 'Peinados para eventos, graduaciones y ocasiones especiales.', 'precio' => 45000, 'duracion_minutos' => 60, 'estado' => 'activo', 'imagen' => 'peinado.jpg'],
            ['nombre_servicio' => 'Tinte completo', 'descripcion' => 'Coloración profesional con evaluación previa del cabello.', 'precio' => 120000, 'duracion_minutos' => 120, 'estado' => 'activo', 'imagen' => 'tinte.jpg'],
            ['nombre_servicio' => 'Maquillaje profesional', 'descripcion' => 'Maquillaje social o para eventos con acabado duradero.', 'precio' => 80000, 'duracion_minutos' => 90, 'estado' => 'activo', 'imagen' => 'maquillaje.jpg'],
            ['nombre_servicio' => 'Manicure semipermanente', 'descripcion' => 'Limpieza, esmaltado semipermanente y acabado brillante.', 'precio' => 50000, 'duracion_minutos' => 60, 'estado' => 'activo', 'imagen' => 'manicure.jpg'],
            ['nombre_servicio' => 'Pedicure spa', 'descripcion' => 'Cuidado profundo de pies con exfoliación e hidratación.', 'precio' => 55000, 'duracion_minutos' => 60, 'estado' => 'activo', 'imagen' => 'pedicure.jpg'],
            ['nombre_servicio' => 'Depilación con cera', 'descripcion' => 'Depilación facial o corporal con protocolos de higiene.', 'precio' => 30000, 'duracion_minutos' => 30, 'estado' => 'activo', 'imagen' => 'depilacion.jpg'],
        ];

        foreach ($services as $service) {
            Servicio::updateOrCreate(['nombre_servicio' => $service['nombre_servicio']], $service);
        }

        Cliente::updateOrCreate(
            ['correo_electronico' => 'cliente@marly.com'],
            [
                'nombre_completo' => 'Cliente de prueba',
                'telefono' => '3001234567',
                'contrasena' => Hash::make('cliente12345'),
                'fecha_registro' => now(),
            ]
        );
    }
}
