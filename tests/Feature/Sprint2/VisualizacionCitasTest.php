<?php

namespace Tests\Feature\Sprint2;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VisualizacionCitasTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $cliente;
    private Trabajador $trabajador;
    private Servicio $servicio;

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

        $this->trabajador = Trabajador::create([
            'nombre_completo' => 'Laura Gómez',
            'especialidad' => 'Maquillaje',
            'foto' => null,
            'anios_experiencia' => 5,
            'calificacion' => 4.8,
            'estado' => 'activo',
        ]);

        $this->servicio = Servicio::create([
            'nombre_servicio' => 'Maquillaje profesional',
            'descripcion' => 'Maquillaje para eventos especiales.',
            'precio' => 80000,
            'duracion_minutos' => 90,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        $cita = Cita::create([
            'id_cliente' => $this->cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => $this->cliente->nombre_completo,
            'telefono_contacto' => $this->cliente->telefono,
            'fecha_cita' => now()->addDay()->format('Y-m-d'),
            'hora_inicio' => '09:00:00',
            'hora_fin' => '10:30:00',
            'duracion_total_minutos' => 90,
            'notas' => 'Cita de prueba',
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
            'area' => 'Maquillaje',
        ]);

        $this->withSession([
            'cliente_id' => $this->cliente->id_cliente,
            'cliente_nombre' => $this->cliente->nombre_completo,
        ]);
    }

    /**
     * CID-1
     * Cuando la administradora acceda al sistema,
     * entonces se debe mostrar el listado de citas registradas.
     *
     * Nota: en el código real del proyecto, esta visualización está asociada al cliente autenticado.
     */
    public function test_hu04_cid1_muestra_el_listado_de_citas_registradas(): void
    {
        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);
        $response->assertSee('Karen Erazo');
    }

    /**
     * CID-2
     * Cuando se visualicen las citas,
     * entonces cada cita debe incluir cliente, servicio, trabajador, fecha y hora.
     */
    public function test_hu04_cid2_muestra_cliente_servicio_trabajador_fecha_y_hora(): void
    {
        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);
        $response->assertSee('Karen Erazo');
        $response->assertSee('Maquillaje profesional');
        $response->assertSee('Laura Gómez');
        $response->assertSee('abril');
        $response->assertSee('2026');
        $response->assertSee('09:00');
    }

    /**
     * CID-3
     * Cuando las citas sean mostradas,
     * entonces deben visualizarse de manera ordenada.
     */
    public function test_hu04_cid3_visualiza_las_citas_ordenadas(): void
    {
        $segundaCita = Cita::create([
            'id_cliente' => $this->cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => $this->cliente->nombre_completo,
            'telefono_contacto' => $this->cliente->telefono,
            'fecha_cita' => now()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '15:00:00',
            'hora_fin' => '16:30:00',
            'duracion_total_minutos' => 90,
            'notas' => 'Segunda cita',
            'estado' => 'registrada',
            'fecha_registro' => now(),
        ]);

        DB::table('cita_servicio')->insert([
            'id_cita' => $segundaCita->id_cita,
            'id_servicio' => $this->servicio->id_servicio,
        ]);

        DB::table('cita_detalle')->insert([
            'id_cita' => $segundaCita->id_cita,
            'id_servicio' => $this->servicio->id_servicio,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'area' => 'Maquillaje',
        ]);

        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '23 de abril',
            '22 de abril',
        ]);
    }

    /**
     * CID-4
     * Cuando la administradora consulte la información,
     * entonces debe poder visualizar las citas desde la plataforma.
     *
     * Nota: en el sistema actual esta consulta la realiza el cliente autenticado.
     */
    public function test_hu04_cid4_permite_visualizar_las_citas_desde_la_plataforma(): void
    {
        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cliente.citas.index');
    }

    /**
     * CID-5
     * Cuando existan múltiples citas,
     * entonces el sistema debe permitir identificar fácilmente la programación de cada una.
     */
    public function test_hu04_cid5_permite_identificar_multiples_citas(): void
    {
        $segundaCita = Cita::create([
            'id_cliente' => $this->cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => $this->cliente->nombre_completo,
            'telefono_contacto' => $this->cliente->telefono,
            'fecha_cita' => now()->addDays(3)->format('Y-m-d'),
            'hora_inicio' => '11:00:00',
            'hora_fin' => '12:30:00',
            'duracion_total_minutos' => 90,
            'notas' => 'Tercera cita',
            'estado' => 'registrada',
            'fecha_registro' => now(),
        ]);

        DB::table('cita_servicio')->insert([
            'id_cita' => $segundaCita->id_cita,
            'id_servicio' => $this->servicio->id_servicio,
        ]);

        DB::table('cita_detalle')->insert([
            'id_cita' => $segundaCita->id_cita,
            'id_servicio' => $this->servicio->id_servicio,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'area' => 'Maquillaje',
        ]);

        $response = $this->get(route('cliente.citas.index'));

        $response->assertStatus(200);
        $response->assertSee('22 de abril');
        $response->assertSee('24 de abril');
        $response->assertSee('09:00');
        $response->assertSee('11:00');
    }
}