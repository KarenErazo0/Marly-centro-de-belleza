<?php

namespace Tests\Feature\Sprint4;

use App\Models\Servicio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActualizacionCatalogoServiciosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * CID-1
     * Cuando la administradora desee agregar un nuevo servicio,
     * entonces el sistema debe permitir registrar nuevos servicios en el catálogo.
     */
    public function test_hu08_cid1_permite_registrar_un_nuevo_servicio_en_el_catalogo(): void
    {
        $response = $this->withSession($this->sesionAdmin())
            ->post(route('admin.servicios.guardar'), [
                'nombre_servicio' => 'Limpieza facial profunda',
                'descripcion' => 'Limpieza facial con hidratación y cuidado de la piel.',
                'precio' => 95000,
                'duracion_minutos' => 90,
                'estado' => '1',
                'imagen' => UploadedFile::fake()->create('limpieza-facial.jpg', 100, 'image/jpeg'),
            ]);

        $response->assertRedirect(route('admin.dashboard', ['tab' => 'servicios']));
        $response->assertSessionHas('success', 'Servicio agregado correctamente.');

        $this->assertDatabaseHas('servicios', [
            'nombre_servicio' => 'Limpieza facial profunda',
            'descripcion' => 'Limpieza facial con hidratación y cuidado de la piel.',
            'precio' => 95000,
            'duracion_minutos' => 90,
            'estado' => 'activo',
        ]);
    }

    /**
     * CID-2
     * Cuando la administradora seleccione un servicio existente,
     * entonces el sistema debe permitir modificar la información del servicio.
     */
    public function test_hu08_cid2_permite_modificar_un_servicio_existente(): void
    {
        $servicio = $this->crearServicio();

        $response = $this->withSession($this->sesionAdmin())
            ->put(route('admin.servicios.actualizar', $servicio), [
                'nombre_servicio' => 'Pedicure spa premium',
                'descripcion' => 'Pedicure con exfoliación, hidratación y masaje.',
                'precio' => 75000,
                'duracion_minutos' => 80,
                'estado' => '1',
            ]);

        $response->assertRedirect(route('admin.dashboard', ['tab' => 'servicios']));
        $response->assertSessionHas('success', 'Servicio actualizado correctamente.');

        $this->assertDatabaseHas('servicios', [
            'id_servicio' => $servicio->id_servicio,
            'nombre_servicio' => 'Pedicure spa premium',
            'descripcion' => 'Pedicure con exfoliación, hidratación y masaje.',
            'precio' => 75000,
            'duracion_minutos' => 80,
            'estado' => 'activo',
        ]);
    }

    /**
     * CID-3
     * Cuando un servicio ya no esté disponible,
     * entonces el sistema debe permitir eliminarlo o desactivarlo.
     */
    public function test_hu08_cid3_permite_desactivar_un_servicio_no_disponible(): void
    {
        $servicio = $this->crearServicio(['estado' => 'activo']);

        $response = $this->withSession($this->sesionAdmin())
            ->patch(route('admin.servicios.estado', $servicio));

        $response->assertRedirect(route('admin.dashboard', ['tab' => 'servicios']));
        $response->assertSessionHas('success', 'Servicio desactivado y oculto para clientes.');

        $this->assertDatabaseHas('servicios', [
            'id_servicio' => $servicio->id_servicio,
            'estado' => 'inactivo',
        ]);
    }

    /**
     * CID-4
     * Cuando se registre o modifique un servicio,
     * entonces cada servicio debe incluir nombre, descripción y precio.
     */
    public function test_hu08_cid4_valida_nombre_descripcion_y_precio_al_guardar_servicio(): void
    {
        $response = $this->withSession($this->sesionAdmin())
            ->from(route('admin.dashboard', ['tab' => 'servicios']))
            ->post(route('admin.servicios.guardar'), [
                'nombre_servicio' => '',
                'descripcion' => '',
                'precio' => '',
                'duracion_minutos' => '',
                'estado' => '1',
                'imagen' => null,
            ]);

        $response->assertRedirect(route('admin.dashboard', ['tab' => 'servicios']));
        $response->assertSessionHasErrors([
            'nombre_servicio',
            'descripcion',
            'precio',
            'duracion_minutos',
            'imagen',
        ]);

        $this->assertDatabaseCount('servicios', 0);
    }

    /**
     * CID-5
     * Cuando se realicen cambios en el catálogo de servicios,
     * entonces el sistema debe reflejar los cambios en la página web.
     */
    public function test_hu08_cid5_refleja_los_cambios_del_catalogo_en_la_pagina_web(): void
    {
        $servicio = $this->crearServicio([
            'nombre_servicio' => 'Servicio visible actualizado',
            'descripcion' => 'Descripción visible para el cliente.',
            'precio' => 88000,
            'estado' => 'activo',
        ]);

        $responseInicio = $this->get(route('inicio'));

        $responseInicio->assertStatus(200);
        $responseInicio->assertSee('Servicio visible actualizado');
        $responseInicio->assertSee('Descripción visible para el cliente.');
        $responseInicio->assertSee('$88.000');

        $this->withSession($this->sesionAdmin())
            ->patch(route('admin.servicios.estado', $servicio));

        $responseInicioDespues = $this->get(route('inicio'));
        $responseInicioDespues->assertDontSee('Servicio visible actualizado');
    }

    private function crearServicio(array $overrides = []): Servicio
    {
        return Servicio::create(array_merge([
            'nombre_servicio' => 'Pedicure spa',
            'descripcion' => 'Servicio de pedicure con hidratación y limpieza.',
            'precio' => 55000,
            'duracion_minutos' => 75,
            'estado' => 'activo',
            'imagen' => null,
        ], $overrides));
    }

    private function sesionAdmin(): array
    {
        return [
            'admin_autenticado' => true,
            'admin_correo' => 'admin@marly.com',
            'admin_nombre' => 'Administradora Marly',
        ];
    }
}
