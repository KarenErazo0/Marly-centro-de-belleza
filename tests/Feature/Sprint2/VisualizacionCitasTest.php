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

class VisualizacionCitasTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $cliente;
    private Trabajador $trabajador;
    private Servicio $servicio;
    private string $fechaPrimeraCita;
    private string $fechaSegundaCita;
    private string $fechaTerceraCita;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setLocale('es');
        Carbon::setTestNow(Carbon::create(2026, 4, 20, 8, 0, 0, 'America/Bogota'));

        $this->prepararCompatibilidadTrabajadores();

        $this->fechaPrimeraCita = now('America/Bogota')->addDay()->format('Y-m-d');
        $this->fechaSegundaCita = now('America/Bogota')->addDays(2)->format('Y-m-d');
        $this->fechaTerceraCita = now('America/Bogota')->addDays(3)->format('Y-m-d');

        $this->cliente = Cliente::create([
            'nombre_completo' => 'Karen Erazo',
            'correo_electronico' => 'karen@test.com',
            'telefono' => '3001234567',
            'contrasena' => bcrypt('password123'),
            'fecha_registro' => now('America/Bogota'),
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

        $this->servicio = Servicio::create([
            'nombre_servicio' => 'Maquillaje profesional',
            'descripcion' => 'Maquillaje para eventos especiales.',
            'precio' => 80000,
            'duracion_minutos' => 90,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        $this->crearCitaConRelaciones(
            $this->fechaPrimeraCita,
            '09:00:00',
            '10:30:00',
            'Cita de prueba'
        );

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

    private function crearCitaConRelaciones(
        string $fecha,
        string $horaInicio,
        string $horaFin,
        ?string $notas = null
    ): Cita {
        $cita = Cita::create([
            'id_cliente' => $this->cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => $this->cliente->nombre_completo,
            'telefono_contacto' => $this->cliente->telefono,
            'fecha_cita' => $fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'duracion_total_minutos' => 90,
            'notas' => $notas,
            'estado' => 'registrada',
            'fecha_registro' => now('America/Bogota'),
        ]);

        DB::table('cita_servicio')->insert([
            'id_cita' => $cita->id_cita,
            'id_servicio' => $this->servicio->id_servicio,
        ]);

        DB::table('cita_detalle')->insert([
            'id_cita' => $cita->id_cita,
            'id_servicio' => $this->servicio->id_servicio,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'area' => 'Maquillaje',
        ]);

        return $cita;
    }

    private function fechaEnPantalla(string $fecha): string
    {
        return ucfirst(Carbon::parse($fecha)->translatedFormat('l, j \d\e F \d\e Y'));
    }

    public function test_hu04_cid1_muestra_el_listado_de_citas_registradas(): void
    {
        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cliente.citas.index');
        $response->assertSee('Karen Erazo');
        $response->assertSee('Maquillaje profesional');
    }

    public function test_hu04_cid2_muestra_cliente_servicio_trabajador_fecha_y_hora(): void
    {
        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);
        $response->assertSee('Karen Erazo');
        $response->assertSee('Maquillaje profesional');
        $response->assertSee('Laura Gómez');
        $response->assertSee($this->fechaEnPantalla($this->fechaPrimeraCita));
        $response->assertSee('09:00 a. m.');
    }

    public function test_hu04_cid3_visualiza_las_citas_ordenadas(): void
    {
        $this->crearCitaConRelaciones(
            $this->fechaSegundaCita,
            '15:00:00',
            '16:30:00',
            'Segunda cita'
        );

        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $this->fechaEnPantalla($this->fechaSegundaCita),
            $this->fechaEnPantalla($this->fechaPrimeraCita),
        ]);
    }

    public function test_hu04_cid4_permite_visualizar_las_citas_desde_la_plataforma(): void
    {
        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cliente.citas.index');
    }

    public function test_hu04_cid5_permite_identificar_multiples_citas(): void
    {
        $this->crearCitaConRelaciones(
            $this->fechaTerceraCita,
            '11:00:00',
            '12:30:00',
            'Tercera cita'
        );

        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);
        $response->assertSee($this->fechaEnPantalla($this->fechaPrimeraCita));
        $response->assertSee($this->fechaEnPantalla($this->fechaTerceraCita));
        $response->assertSee('09:00 a. m.');
        $response->assertSee('11:00 a. m.');
    }
}