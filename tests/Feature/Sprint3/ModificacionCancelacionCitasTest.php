<?php

namespace Tests\Feature\Sprint3;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ModificacionCancelacionCitasTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $cliente;
    private Cliente $otroCliente;
    private Servicio $servicio;
    private Trabajador $trabajador;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cliente = Cliente::create([
            'nombre_completo' => 'Johan Serrano',
            'correo_electronico' => 'johan@test.com',
            'telefono' => '3193676791',
            'contrasena' => bcrypt('password123'),
            'fecha_registro' => now(),
        ]);

        $this->otroCliente = Cliente::create([
            'nombre_completo' => 'Karen Erazo',
            'correo_electronico' => 'karen@test.com',
            'telefono' => '3228302874',
            'contrasena' => bcrypt('password123'),
            'fecha_registro' => now(),
        ]);

        $this->servicio = Servicio::create([
            'nombre_servicio' => 'Manicure semipermanente',
            'descripcion' => 'Servicio de manicure con esmalte semipermanente.',
            'precio' => 45000,
            'duracion_minutos' => 60,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        $this->trabajador = Trabajador::create([
            'nombre_completo' => 'Ana Rodríguez',
            'especialidad' => 'Manicure',
            'foto' => null,
            'estado' => 'activo',
        ]);

        DB::table('trabajador_servicio')->insert([
            'id_trabajador' => $this->trabajador->id_trabajador,
            'id_servicio' => $this->servicio->id_servicio,
        ]);

        $this->withSession([
            'cliente_id' => $this->cliente->id_cliente,
            'cliente_nombre' => $this->cliente->nombre_completo,
        ]);
    }

    /**
     * CID-1
     * Cuando el cliente consulte sus citas agendadas,
     * entonces el sistema debe mostrar las citas previamente registradas.
     */
    public function test_hu06_cid1_muestra_las_citas_previamente_registradas_del_cliente(): void
    {
        $this->crearCita($this->cliente, now()->addDays(2)->format('Y-m-d'), '09:00:00', '10:00:00');
        $this->crearCita($this->otroCliente, now()->addDays(2)->format('Y-m-d'), '11:00:00', '12:00:00', 'Cita de otro cliente');

        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);
        $response->assertSee('Manicure semipermanente');
        $response->assertSee('Johan Serrano');
        $response->assertDontSee('Cita de otro cliente');
    }

    /**
     * CID-2
     * Cuando el cliente necesite modificar una cita,
     * entonces el sistema debe permitir realizar cambios.
     */
    public function test_hu06_cid2_permite_modificar_una_cita_agendada(): void
    {
        $cita = $this->crearCita($this->cliente, now()->addDays(3)->format('Y-m-d'), '09:00:00', '10:00:00');
        $nuevaFecha = now()->addDays(4)->format('Y-m-d');

        $response = $this->put(route('cliente.citas.actualizar', $cita), [
            'fecha' => $nuevaFecha,
            'hora' => '12:00',
            'telefono_contacto' => '3009998888',
            'notas' => 'Cambio de horario solicitado por el cliente.',
        ]);

        $response->assertRedirect(route('cliente.citas.index'));
        $response->assertSessionHas('success', 'Tu cita fue modificada correctamente.');

        $this->assertDatabaseHas('citas', [
            'id_cita' => $cita->id_cita,
            'fecha_cita' => $nuevaFecha,
            'hora_inicio' => '12:00:00',
            'hora_fin' => '13:00:00',
            'telefono_contacto' => '3009998888',
            'estado' => 'registrada',
        ]);
    }

    /**
     * CID-3
     * Cuando el cliente seleccione una cita previamente registrada,
     * entonces el sistema debe permitir cancelar la cita.
     */
    public function test_hu06_cid3_permite_cancelar_una_cita_previamente_registrada(): void
    {
        $cita = $this->crearCita($this->cliente, now()->addDays(3)->format('Y-m-d'), '09:00:00', '10:00:00');

        $response = $this->delete(route('cliente.citas.cancelar', $cita));

        $response->assertRedirect(route('cliente.citas.index'));
        $response->assertSessionHas('success', 'Tu cita fue cancelada correctamente.');

        $this->assertDatabaseHas('citas', [
            'id_cita' => $cita->id_cita,
            'estado' => 'cancelada',
        ]);
    }

    /**
     * CID-4
     * Cuando el cliente realice una modificación de la cita,
     * entonces el sistema debe validar la disponibilidad antes de guardar los cambios.
     */
    public function test_hu06_cid4_valida_disponibilidad_antes_de_guardar_la_modificacion(): void
    {
        $fecha = now()->addDays(5)->format('Y-m-d');
        $citaCliente = $this->crearCita($this->cliente, $fecha, '09:00:00', '10:00:00');
        $this->crearCita($this->otroCliente, $fecha, '12:00:00', '13:00:00');

        $response = $this->from(route('cliente.citas.editar', $citaCliente))->put(route('cliente.citas.actualizar', $citaCliente), [
            'fecha' => $fecha,
            'hora' => '12:00',
            'telefono_contacto' => '3193676791',
            'notas' => 'Intento de horario ocupado.',
        ]);

        $response->assertRedirect(route('cliente.citas.editar', $citaCliente));
        $response->assertSessionHas('error', 'La hora seleccionada no está disponible.');

        $this->assertDatabaseHas('citas', [
            'id_cita' => $citaCliente->id_cita,
            'hora_inicio' => '09:00:00',
        ]);
    }

    /**
     * CID-5
     * Cuando la cita sea modificada o cancelada correctamente,
     * entonces el sistema debe mostrar una confirmación de la acción realizada.
     */
    public function test_hu06_cid5_muestra_confirmacion_al_modificar_o_cancelar_la_cita(): void
    {
        $cita = $this->crearCita($this->cliente, now()->addDays(6)->format('Y-m-d'), '09:00:00', '10:00:00');

        $respuestaModificacion = $this->put(route('cliente.citas.actualizar', $cita), [
            'fecha' => now()->addDays(7)->format('Y-m-d'),
            'hora' => '11:00',
            'telefono_contacto' => '3193676791',
            'notas' => null,
        ]);

        $respuestaModificacion->assertSessionHas('success', 'Tu cita fue modificada correctamente.');

        $respuestaCancelacion = $this->delete(route('cliente.citas.cancelar', $cita->fresh()));

        $respuestaCancelacion->assertSessionHas('success', 'Tu cita fue cancelada correctamente.');
    }

    private function crearCita(Cliente $cliente, string $fecha, string $horaInicio, string $horaFin, ?string $nombre = null): Cita
    {
        $cita = Cita::create([
            'id_cliente' => $cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => $nombre ?? $cliente->nombre_completo,
            'telefono_contacto' => $cliente->telefono,
            'fecha_cita' => $fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'duracion_total_minutos' => 60,
            'notas' => null,
            'estado' => 'registrada',
            'fecha_registro' => now(),
        ]);

        DB::table('cita_servicio')->insert([
            'id_cita' => $cita->id_cita,
            'id_servicio' => $this->servicio->id_servicio,
        ]);

        DB::table('cita_detalle')->insert([
            'id_cita' => $cita->id_cita,
            'id_servicio' => $this->servicio->id_servicio,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'area' => 'Manicure',
        ]);

        return $cita;
    }
}
