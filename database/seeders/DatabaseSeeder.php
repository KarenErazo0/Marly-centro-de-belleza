<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajador;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        $servicios = Servicio::pluck('id_servicio', 'nombre_servicio');

        $trabajadores = [
            ['nombre_completo' => 'María González', 'especialidad' => 'Especialista en uñas', 'anios_experiencia' => 8, 'total_resenas' => 127, 'calificacion' => 4.9, 'foto' => 'manicure.jpg', 'servicios' => ['Manicure semipermanente', 'Pedicure spa']],
            ['nombre_completo' => 'Camila Rosero', 'especialidad' => 'Especialista en uñas', 'anios_experiencia' => 6, 'total_resenas' => 96, 'calificacion' => 4.8, 'foto' => 'pedicure.jpg', 'servicios' => ['Manicure semipermanente', 'Pedicure spa']],
            ['nombre_completo' => 'Juliana Paz', 'especialidad' => 'Técnica de uñas', 'anios_experiencia' => 5, 'total_resenas' => 89, 'calificacion' => 4.7, 'foto' => 'manicure.jpg', 'servicios' => ['Manicure semipermanente', 'Pedicure spa']],
            ['nombre_completo' => 'Valentina Coral', 'especialidad' => 'Técnica de uñas', 'anios_experiencia' => 7, 'total_resenas' => 104, 'calificacion' => 5.0, 'foto' => 'pedicure.jpg', 'servicios' => ['Manicure semipermanente', 'Pedicure spa']],

            ['nombre_completo' => 'Laura Martínez', 'especialidad' => 'Estilista senior', 'anios_experiencia' => 10, 'total_resenas' => 98, 'calificacion' => 5.0, 'foto' => 'tinte.jpg', 'servicios' => ['Peinado social', 'Tinte completo']],
            ['nombre_completo' => 'Ana Rodríguez', 'especialidad' => 'Maquillaje profesional', 'anios_experiencia' => 6, 'total_resenas' => 156, 'calificacion' => 4.8, 'foto' => 'maquillaje.jpg', 'servicios' => ['Maquillaje profesional', 'Peinado social']],
            ['nombre_completo' => 'Carolina López', 'especialidad' => 'Peinados y recogidos', 'anios_experiencia' => 7, 'total_resenas' => 143, 'calificacion' => 4.9, 'foto' => 'peinado.jpg', 'servicios' => ['Peinado social', 'Tinte completo']],
            ['nombre_completo' => 'Daniela Ruiz', 'especialidad' => 'Colorista', 'anios_experiencia' => 9, 'total_resenas' => 112, 'calificacion' => 4.9, 'foto' => 'tinte.jpg', 'servicios' => ['Tinte completo', 'Peinado social']],
            ['nombre_completo' => 'Sofía Herrera', 'especialidad' => 'Estilista integral', 'anios_experiencia' => 8, 'total_resenas' => 135, 'calificacion' => 4.9, 'foto' => 'maquillaje.jpg', 'servicios' => ['Peinado social', 'Tinte completo', 'Maquillaje profesional']],

            ['nombre_completo' => 'Paula Insuasti', 'especialidad' => 'Depilación con cera', 'anios_experiencia' => 5, 'total_resenas' => 73, 'calificacion' => 4.8, 'foto' => 'depilacion.jpg', 'servicios' => ['Depilación con cera']],
            ['nombre_completo' => 'Andrea Benavides', 'especialidad' => 'Especialista en depilación', 'anios_experiencia' => 6, 'total_resenas' => 80, 'calificacion' => 4.9, 'foto' => 'depilacion.jpg', 'servicios' => ['Depilación con cera']],
        ];

        foreach ($trabajadores as $data) {
            $serviciosAsignados = $data['servicios'];
            unset($data['servicios']);

            $trabajador = Trabajador::updateOrCreate(
                ['nombre_completo' => $data['nombre_completo']],
                $data + ['estado' => 'activo']
            );

            $trabajador->servicios()->sync(
                collect($serviciosAsignados)
                    ->map(fn ($nombre) => $servicios[$nombre] ?? null)
                    ->filter()
                    ->values()
                    ->all()
            );
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
