<?php

namespace Tests\Feature\Sprint1;

use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistroClienteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * CID-1
     * Cuando el cliente envía correctamente el formulario con datos válidos,
     * entonces el sistema guarda correctamente la información del cliente.
     */
    public function test_hu02_cid1_guarda_correctamente_el_cliente_con_datos_validos(): void
    {
        $response = $this->post(route('cliente.registro.guardar'), [
            'nombre_completo' => 'Karen Erazo',
            'correo_electronico' => 'karen@example.com',
            'telefono' => '3001234567',
            'contrasena' => 'password123',
            'contrasena_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('cliente.cuenta'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('clientes', [
            'nombre_completo' => 'Karen Erazo',
            'correo_electronico' => 'karen@example.com',
            'telefono' => '3001234567',
        ]);

        $cliente = Cliente::where('correo_electronico', 'karen@example.com')->first();

        $this->assertNotNull($cliente);
        $this->assertTrue(Hash::check('password123', $cliente->contrasena));
        $this->assertEquals('Karen Erazo', session('cliente_nombre'));
        $this->assertEquals($cliente->id_cliente, session('cliente_id'));
    }

    /**
     * CID-2
     * Cuando el cliente ingresa un correo no válido,
     * entonces el sistema debe mostrar un mensaje de que el correo
     * no tiene un formato válido sin guardar los datos.
     */
    public function test_hu02_cid2_no_guarda_el_cliente_si_el_correo_no_es_valido(): void
    {
        $response = $this->from(route('cliente.registro'))->post(route('cliente.registro.guardar'), [
            'nombre_completo' => 'Karen Erazo',
            'correo_electronico' => 'correo-invalido',
            'telefono' => '3001234567',
            'contrasena' => 'password123',
            'contrasena_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('cliente.registro'));
        $response->assertSessionHasErrors('correo_electronico');

        $this->assertDatabaseMissing('clientes', [
            'correo_electronico' => 'correo-invalido',
        ]);
    }

    /**
     * CID-3
     * Cuando el cliente ingresa un correo electrónico ya existente,
     * entonces el sistema valida que el correo no esté registrado previamente
     * y muestra el mensaje correspondiente.
     */
    public function test_hu02_cid3_no_permite_registrar_un_correo_ya_existente(): void
    {
        Cliente::create([
            'nombre_completo' => 'Cliente Existente',
            'correo_electronico' => 'existente@example.com',
            'telefono' => '3000000000',
            'contrasena' => Hash::make('password123'),
            'fecha_registro' => now(),
        ]);

        $response = $this->from(route('cliente.registro'))->post(route('cliente.registro.guardar'), [
            'nombre_completo' => 'Karen Erazo',
            'correo_electronico' => 'existente@example.com',
            'telefono' => '3001234567',
            'contrasena' => 'password123',
            'contrasena_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('cliente.registro'));
        $response->assertSessionHasErrors('correo_electronico');

        $this->assertDatabaseCount('clientes', 1);
    }

    /**
     * CID-4
     * Cuando el cliente deja sin diligenciar algún dato en el registro,
     * entonces el sistema no guarda la información de registro
     * y genera un mensaje indicando que debe llenar el dato correspondiente.
     */
    public function test_hu02_cid4_no_guarda_el_registro_si_hay_campos_obligatorios_vacios(): void
    {
        $response = $this->from(route('cliente.registro'))->post(route('cliente.registro.guardar'), [
            'nombre_completo' => '',
            'correo_electronico' => '',
            'telefono' => '',
            'contrasena' => '',
            'contrasena_confirmation' => '',
        ]);

        $response->assertRedirect(route('cliente.registro'));
        $response->assertSessionHasErrors([
            'nombre_completo',
            'correo_electronico',
            'telefono',
            'contrasena',
        ]);

        $this->assertDatabaseCount('clientes', 0);
    }

    /**
     * Validación extra útil:
     * no debe guardar si la confirmación de contraseña no coincide.
     */
    public function test_hu02_no_guarda_si_la_confirmacion_de_contrasena_no_coincide(): void
    {
        $response = $this->from(route('cliente.registro'))->post(route('cliente.registro.guardar'), [
            'nombre_completo' => 'Karen Erazo',
            'correo_electronico' => 'karen2@example.com',
            'telefono' => '3001234567',
            'contrasena' => 'password123',
            'contrasena_confirmation' => 'password456',
        ]);

        $response->assertRedirect(route('cliente.registro'));
        $response->assertSessionHasErrors('contrasena');

        $this->assertDatabaseMissing('clientes', [
            'correo_electronico' => 'karen2@example.com',
        ]);
    }
}