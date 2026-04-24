<?php

namespace Tests\Feature\Sprint1;

use App\Models\Servicio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisualizacionCatalogoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * CID-1
     * Cuando el cliente ingresa a la página principal del sistema,
     * entonces el sistema muestra el listado de los servicios disponibles del salón.
     */
    public function test_hu01_cid1_muestra_el_listado_de_servicios_disponibles_en_la_pagina_principal(): void
    {
        Servicio::create([
            'nombre_servicio' => 'Manicure semipermanente',
            'descripcion' => 'Servicio de manicure con esmalte semipermanente.',
            'precio' => 45000,
            'duracion_minutos' => 60,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        Servicio::create([
            'nombre_servicio' => 'Pedicure spa',
            'descripcion' => 'Servicio de pedicure con hidratación y limpieza.',
            'precio' => 55000,
            'duracion_minutos' => 75,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        $response = $this->get(route('inicio'));

        $response->assertStatus(200);
        $response->assertSee('Manicure semipermanente');
        $response->assertSee('Pedicure spa');
    }

    /**
     * CID-2
     * Cuando el catálogo se visualiza en la plataforma,
     * entonces cada servicio muestra como mínimo su nombre, descripción y precio.
     */
    public function test_hu01_cid2_cada_servicio_muestra_nombre_descripcion_y_precio(): void
    {
        Servicio::create([
            'nombre_servicio' => 'Maquillaje profesional',
            'descripcion' => 'Maquillaje para eventos especiales.',
            'precio' => 80000,
            'duracion_minutos' => 90,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        $response = $this->get(route('inicio'));

        $response->assertStatus(200);
        $response->assertSee('Maquillaje profesional');
        $response->assertSee('Maquillaje para eventos especiales.');
        $response->assertSee('$80.000');
    }

    /**
     * CID-3
     * Cuando el cliente aún no ha iniciado sesión,
     * entonces puede visualizar el catálogo sin necesidad de autenticarse.
     */
    public function test_hu01_cid3_el_cliente_puede_visualizar_el_catalogo_sin_autenticarse(): void
    {
        Servicio::create([
            'nombre_servicio' => 'Depilación con cera',
            'descripcion' => 'Depilación corporal con cera.',
            'precio' => 35000,
            'duracion_minutos' => 40,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        $response = $this->get(route('inicio'));

        $response->assertStatus(200);
        $response->assertSee('Depilación con cera');
        $this->assertGuest();
    }

    /**
     * Validación extra útil:
     * solo deben mostrarse servicios activos.
     */
    public function test_hu01_solo_muestra_servicios_activos(): void
    {
        Servicio::create([
            'nombre_servicio' => 'Peinado social',
            'descripcion' => 'Peinado elegante para ocasión especial.',
            'precio' => 60000,
            'duracion_minutos' => 60,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        Servicio::create([
            'nombre_servicio' => 'Servicio inactivo',
            'descripcion' => 'Este servicio no debería mostrarse.',
            'precio' => 99999,
            'duracion_minutos' => 30,
            'estado' => 'inactivo',
            'imagen' => null,
        ]);

        $response = $this->get(route('inicio'));

        $response->assertStatus(200);
        $response->assertSee('Peinado social');
        $response->assertDontSee('Servicio inactivo');
    }
}