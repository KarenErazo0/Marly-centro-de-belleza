<?php

namespace Tests\Feature\Sprint2;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajador;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AgendamientoCitasTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $cliente;
    private Servicio $servicio;
    private Trabajador $trabajador;
    private string $fechaCita;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 4, 20, 8, 0, 0, 'America/Bogota'));

        $this->fechaCita = now('America/Bogota')->addDay()->format('Y-m-d');

        $this->prepararCompatibilidadTrabajadores();

        $this->cliente = Cliente::create([
            'nombre_completo' => 'Karen Erazo',
            'correo_electronico' => 'karen@test.com',
            'telefono' => '3001234567',
            'contrasena' => bcrypt('password123'),
            'fecha_registro' => now('America/Bogota'),
        ]);

        $this->servicio = Servicio::create([
            'nombre_servicio' => 'Maquillaje profesional',
            'descripcion' => 'Maquillaje para eventos especiales.',
            'precio' => 80000,
            'duracion_minutos' => 90,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        $datosTrabajador = [
            'nombre_completo' => 'Laura Gómez',
            'especialidad' => 'Maquillaje',
            'foto' => null,
            'estado' => 'activo',
        ];

        if (Schema::hasColumn('trabajadores', 'anios_experiencia')) {
            $datosTrabajador['anios_experiencia'] = 5;
        }

        if (Schema::hasColumn('trabajadores', 'calificacion')) {
            $datosTrabajador['calificacion'] = 4.8;
        }

        $this->trabajador = Trabajador::create($datosTrabajador);

        DB::table('trabajador_servicio')->insert([
            'id_trabajador' => $this->trabajador->id_trabajador,
            'id_servicio' => $this->servicio->id_servicio,
        ]);

        $this->withSession([
            'cliente_id' => $this->cliente->id_cliente,
            'cliente_nombre' => $this->cliente->nombre_completo,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function prepararCompatibilidadTrabajadores(): void
    {
        if (! Schema::hasColumn('trabajadores', 'calificacion')) {
            Schema::table('trabajadores', function ($table) {
                $table->decimal('calificacion', 2, 1)->default(5.0)->nullable();
            });
        }
    }

    private function sesionReserva(string $hora): array
    {
        return [
            'reserva_cita' => [
                'servicios' => [$this->servicio->id_servicio],
                'trabajador' => $this->trabajador->id_trabajador,
                'trabajadores' => [
                    'maquillaje' => $this->trabajador->id_trabajador,
                ],
                'fecha' => $this->fechaCita,
                'hora' => $hora,
                'nombre_cliente' => $this->cliente->nombre_completo,
            ],
            'cliente_id' => $this->cliente->id_cliente,
            'cliente_nombre' => $this->cliente->nombre_completo,
        ];
    }

    public function test_hu03_cid1_permite_registrar_la_cita_con_datos_validos(): void
    {
        $this->withSession($this->sesionReserva('10:00'));

        $response = $this->post(route('cliente.citas.agendar.registrar'), [
            'nombre_cliente' => 'Karen Erazo',
            'telefono_contacto' => '3001234567',
            'notas' => 'Prueba de agendamiento',
        ]);

        $response->assertRedirect(route('cliente.citas.index'));
        $response->assertSessionHas('success', 'Tu cita fue registrada correctamente.');

        $this->assertDatabaseHas('citas', [
            'id_cliente' => $this->cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => 'Karen Erazo',
            'telefono_contacto' => '3001234567',
            'fecha_cita' => $this->fechaCita,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '11:30:00',
            'estado' => 'registrada',
        ]);
    }

    public function test_hu03_cid2_asocia_el_servicio_a_la_cita(): void
    {
        $this->withSession($this->sesionReserva('11:00'));

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

        $this->assertDatabaseHas('cita_detalle', [
            'id_cita' => $cita->id_cita,
            'id_servicio' => $this->servicio->id_servicio,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'area' => 'Maquillaje',
        ]);
    }

    public function test_hu03_cid3_muestra_solo_horarios_disponibles(): void
    {
        Cita::create([
            'id_cliente' => $this->cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => 'Karen Erazo',
            'telefono_contacto' => '3001234567',
            'fecha_cita' => $this->fechaCita,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '11:30:00',
            'duracion_total_minutos' => 90,
            'notas' => null,
            'estado' => 'registrada',
            'fecha_registro' => now('America/Bogota'),
        ]);

        $this->withSession([
            'reserva_cita' => [
                'servicios' => [$this->servicio->id_servicio],
                'trabajador' => $this->trabajador->id_trabajador,
                'trabajadores' => [
                    'maquillaje' => $this->trabajador->id_trabajador,
                ],
            ],
            'cliente_id' => $this->cliente->id_cliente,
            'cliente_nombre' => $this->cliente->nombre_completo,
        ]);

        $response = $this->get(route('cliente.citas.agendar.horario', [
            'fecha' => $this->fechaCita,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('cliente.citas.horario');
        $response->assertDontSee('10:00 a. m.');
        $response->assertSee('12:00 p. m.');
    }

    public function test_hu03_cid4_asigna_el_trabajador_a_la_reserva(): void
    {
        $this->withSession([
            'reserva_cita' => [
                'servicios' => [$this->servicio->id_servicio],
            ],
            'cliente_id' => $this->cliente->id_cliente,
            'cliente_nombre' => $this->cliente->nombre_completo,
        ]);

        $response = $this->post(route('cliente.citas.agendar.trabajador.guardar'), [
            'accion' => 'manual',
            'trabajadores' => [
                'maquillaje' => $this->trabajador->id_trabajador,
            ],
        ]);

        $response->assertRedirect(route('cliente.citas.agendar.horario'));
        $response->assertSessionHas('success', 'Profesionales seleccionados correctamente.');

        $this->assertEquals(
            $this->trabajador->id_trabajador,
            session('reserva_cita.trabajador')
        );

        $this->assertEquals(
            $this->trabajador->id_trabajador,
            session('reserva_cita.trabajadores.maquillaje')
        );
    }

    public function test_hu03_cid5_muestra_confirmacion_despues_del_registro(): void
    {
        $this->withSession($this->sesionReserva('13:00'));

        $response = $this->post(route('cliente.citas.agendar.registrar'), [
            'nombre_cliente' => 'Karen Erazo',
            'telefono_contacto' => '3001234567',
            'notas' => 'Confirmación de prueba',
        ]);

        $response->assertRedirect(route('cliente.citas.index'));
        $response->assertSessionHas('success', 'Tu cita fue registrada correctamente.');
    }
}