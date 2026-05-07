<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajador;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'nombre_servicio' => 'Peinado social',
                'descripcion' => 'Peinados para eventos, graduaciones y ocasiones especiales.',
                'precio' => 45000,
                'duracion_minutos' => 60,
                'estado' => 'activo',
                'imagen' => 'peinado.jpg',
            ],
            [
                'nombre_servicio' => 'Tinte completo',
                'descripcion' => 'Coloración profesional con evaluación previa del cabello.',
                'precio' => 120000,
                'duracion_minutos' => 120,
                'estado' => 'activo',
                'imagen' => 'tinte.jpg',
            ],
            [
                'nombre_servicio' => 'Maquillaje profesional',
                'descripcion' => 'Maquillaje social o para eventos con acabado duradero.',
                'precio' => 80000,
                'duracion_minutos' => 90,
                'estado' => 'activo',
                'imagen' => 'maquillaje.jpg',
            ],
            [
                'nombre_servicio' => 'Manicure semipermanente',
                'descripcion' => 'Limpieza, esmaltado semipermanente y acabado brillante.',
                'precio' => 50000,
                'duracion_minutos' => 60,
                'estado' => 'activo',
                'imagen' => 'manicure.jpg',
            ],
            [
                'nombre_servicio' => 'Pedicure spa',
                'descripcion' => 'Cuidado profundo de pies con exfoliación e hidratación.',
                'precio' => 55000,
                'duracion_minutos' => 60,
                'estado' => 'activo',
                'imagen' => 'pedicure.jpg',
            ],
            [
                'nombre_servicio' => 'Depilación con cera',
                'descripcion' => 'Depilación facial o corporal con protocolos de higiene.',
                'precio' => 30000,
                'duracion_minutos' => 30,
                'estado' => 'activo',
                'imagen' => 'depilacion.jpg',
            ],
        ];

        foreach ($services as $service) {
            Servicio::updateOrCreate(
                ['nombre_servicio' => $service['nombre_servicio']],
                $service
            );
        }

        $servicios = Servicio::pluck('id_servicio', 'nombre_servicio');

        $trabajadores = [
            [
                'nombre_completo' => 'María González',
                'especialidad' => 'Especialista en uñas',
                'foto' => 'estilista1.jpg',
                'servicios' => ['Manicure semipermanente', 'Pedicure spa'],
            ],
            [
                'nombre_completo' => 'Camila Rosero',
                'especialidad' => 'Especialista en uñas',
                'foto' => 'estilista2.jpg',
                'servicios' => ['Manicure semipermanente', 'Pedicure spa'],
            ],
            [
                'nombre_completo' => 'Juliana Paz',
                'especialidad' => 'Técnica de uñas',
                'foto' => 'estilista3.jpg',
                'servicios' => ['Manicure semipermanente', 'Pedicure spa'],
            ],
            [
                'nombre_completo' => 'Valentina Coral',
                'especialidad' => 'Técnica de uñas',
                'foto' => 'estilista4.jpg',
                'servicios' => ['Manicure semipermanente', 'Pedicure spa'],
            ],
            [
                'nombre_completo' => 'Laura Martínez',
                'especialidad' => 'Estilista senior',
                'foto' => 'estilista5.jpg',
                'servicios' => ['Peinado social', 'Tinte completo'],
            ],
            [
                'nombre_completo' => 'Ana Rodríguez',
                'especialidad' => 'Maquillaje profesional',
                'foto' => 'estilista5.jpg',
                'servicios' => ['Maquillaje profesional', 'Peinado social'],
            ],
            [
                'nombre_completo' => 'Carolina López',
                'especialidad' => 'Peinados y recogidos',
                'foto' => 'estilista6.jpg',
                'servicios' => ['Peinado social', 'Tinte completo'],
            ],
            [
                'nombre_completo' => 'Daniela Ruiz',
                'especialidad' => 'Colorista',
                'foto' => 'estilista7.jpg',
                'servicios' => ['Tinte completo', 'Peinado social'],
            ],
            [
                'nombre_completo' => 'Sofía Herrera',
                'especialidad' => 'Estilista integral',
                'foto' => 'estilista1.jpg',
                'servicios' => ['Peinado social', 'Tinte completo', 'Maquillaje profesional'],
            ],
            [
                'nombre_completo' => 'Paula Insuasti',
                'especialidad' => 'Depilación con cera',
                'foto' => 'estilista1.jpg',
                'servicios' => ['Depilación con cera'],
            ],
            [
                'nombre_completo' => 'Andrea Benavides',
                'especialidad' => 'Especialista en depilación',
                'foto' => 'estilista2.jpg',
                'servicios' => ['Depilación con cera'],
            ],
        ];

        foreach ($trabajadores as $data) {
            $serviciosAsignados = $data['servicios'] ?? [];
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

        Cliente::updateOrCreate(
            ['correo_electronico' => 'admin@marly.com'],
            [
                'nombre_completo' => 'Administradora Marly',
                'telefono' => '3150000000',
                'contrasena' => Hash::make('admin12345'),
                'fecha_registro' => now(),
            ]
        );
    }
}