<?php

namespace Tests\Feature\Sprint2;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AgendamientoCitasTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $cliente;
    private Servicio $servicio;
    private Trabajador $trabajador;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cliente = Cliente::create([
            'nombre_completo' => 'Karen Erazo',
            'correo_electronico' => 'karen@test.com',
            'telefono' => '3001234567',
            'contrasena' => bcrypt('password123'),
            'fecha_registro' => now(),
        ]);

        $this->servicio = Servicio::create([
            'nombre_servicio' => 'Maquillaje profesional',
            'descripcion' => 'Maquillaje para eventos especiales.',
            'precio' => 80000,
            'duracion_minutos' => 90,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        $this->trabajador = Trabajador::create([
            'nombre_completo' => 'Laura Gómez',
            'especialidad' => 'Maquillaje',
            'foto' => null,
            'anios_experiencia' => 5,
            'calificacion' => 4.8,
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
     * Cuando el usuario ingrese los datos de la cita,
     * entonces el sistema debe permitir registrar la cita.
     */
    public function test_hu03_cid1_permite_registrar_la_cita_con_datos_validos(): void
    {
        $this->withSession([
            'reserva_cita' => [
                'servicios' => [$this->servicio->id_servicio],
                'trabajador' => $this->trabajador->id_trabajador,
                'trabajadores' => ['maquillaje' => $this->trabajador->id_trabajador],
                'fecha' => now()->addDay()->format('Y-m-d'),
                'hora' => '10:00',
                'nombre_cliente' => $this->cliente->nombre_completo,
            ],
        ]);

        $response = $this->post(route('cliente.citas.agendar.registrar'), [
            'nombre_cliente' => 'Karen Erazo',
            'telefono_contacto' => '3001234567',
            'notas' => 'Prueba de agendamiento',
        ]);

        $response->assertRedirect(route('cliente.citas.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('citas', [
            'id_cliente' => $this->cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => 'Karen Erazo',
            'telefono_contacto' => '3001234567',
            'estado' => 'registrada',
        ]);
    }

    /**
     * CID-2
     * Cuando el usuario seleccione un servicio,
     * entonces el sistema debe asociar el servicio a la cita.
     */
    public function test_hu03_cid2_asocia_el_servicio_a_la_cita(): void
    {
        $this->withSession([
            'reserva_cita' => [
                'servicios' => [$this->servicio->id_servicio],
                'trabajador' => $this->trabajador->id_trabajador,
                'trabajadores' => ['maquillaje' => $this->trabajador->id_trabajador],
                'fecha' => now()->addDay()->format('Y-m-d'),
                'hora' => '11:00',
                'nombre_cliente' => $this->cliente->nombre_completo,
            ],
        ]);

        $this->post(route('cliente.citas.agendar.registrar'), [
            'nombre_cliente' => 'Karen Erazo',
            'telefono_contacto' => '3001234567',
            'notas' => null,
        ]);

        $cita = Cita::latest('id_cita')->first();

        $this->assertNotNull($cita);
        $this->assertDatabaseHas('cita_servicio', [
            'id_cita' => $cita->id_cita,
            'id_servicio' => $this->servicio->id_servicio,
        ]);
    }

    /**
     * CID-3
     * Cuando el usuario consulte horarios,
     * entonces el sistema debe mostrar solo horarios disponibles.
     */
    public function test_hu03_cid3_muestra_solo_horarios_disponibles(): void
    {
        Cita::create([
            'id_cliente' => $this->cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => 'Karen Erazo',
            'telefono_contacto' => '3001234567',
            'fecha_cita' => now()->addDay()->format('Y-m-d'),
            'hora_inicio' => '10:00:00',
            'hora_fin' => '11:30:00',
            'duracion_total_minutos' => 90,
            'notas' => null,
            'estado' => 'registrada',
            'fecha_registro' => now(),
        ]);

        $this->withSession([
            'reserva_cita' => [
                'servicios' => [$this->servicio->id_servicio],
                'trabajador' => $this->trabajador->id_trabajador,
                'trabajadores' => ['maquillaje' => $this->trabajador->id_trabajador],
            ],
        ]);

        $response = $this->get(route('cliente.citas.agendar.horario', [
            'fecha' => now()->addDay()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('10:00');
        $response->assertSee('12:00');
    }

    /**
     * CID-4
     * Cuando el usuario seleccione un trabajador,
     * entonces el sistema debe asignar el trabajador a la cita.
     */
    public function test_hu03_cid4_asigna_el_trabajador_a_la_reserva(): void
    {
        $this->withSession([
            'reserva_cita' => [
                'servicios' => [$this->servicio->id_servicio],
            ],
        ]);

        $response = $this->post(route('cliente.citas.agendar.trabajador.guardar'), [
            'accion' => 'manual',
            'trabajadores' => [
                'maquillaje' => $this->trabajador->id_trabajador,
            ],
        ]);

        $response->assertRedirect(route('cliente.citas.agendar.horario'));
        $response->assertSessionHas('success');

        $this->assertEquals(
            $this->trabajador->id_trabajador,
            session('reserva_cita.trabajador')
        );

        $this->assertEquals(
            $this->trabajador->id_trabajador,
            session('reserva_cita.trabajadores.maquillaje')
        );
    }

    /**
     * CID-5
     * Cuando la cita sea registrada correctamente,
     * entonces el sistema debe mostrar una confirmación de agendamiento.
     */
    public function test_hu03_cid5_muestra_confirmacion_despues_del_registro(): void
    {
        $this->withSession([
            'reserva_cita' => [
                'servicios' => [$this->servicio->id_servicio],
                'trabajador' => $this->trabajador->id_trabajador,
                'trabajadores' => ['maquillaje' => $this->trabajador->id_trabajador],
                'fecha' => now()->addDay()->format('Y-m-d'),
                'hora' => '13:00',
                'nombre_cliente' => $this->cliente->nombre_completo,
            ],
        ]);

        $response = $this->post(route('cliente.citas.agendar.registrar'), [
            'nombre_cliente' => 'Karen Erazo',
            'telefono_contacto' => '3001234567',
            'notas' => 'Confirmación de prueba',
        ]);

        $response->assertRedirect(route('cliente.citas.index'));
        $response->assertSessionHas('success', 'Tu cita fue registrada correctamente.');
    }
}