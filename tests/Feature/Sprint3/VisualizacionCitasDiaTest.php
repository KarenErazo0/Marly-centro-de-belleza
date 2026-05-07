<?php

namespace Tests\Feature\Sprint3;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VisualizacionCitasDiaTest extends TestCase
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
            'estado' => 'activo',
        ]);

        DB::table('trabajador_servicio')->insert([
            'id_trabajador' => $this->trabajador->id_trabajador,
            'id_servicio' => $this->servicio->id_servicio,
        ]);
    }

    /**
     * CID-1
     * Cuando la administradora consulte la agenda diaria,
     * entonces el sistema debe mostrar únicamente las citas correspondientes al día actual.
     */
    public function test_hu05_cid1_muestra_unicamente_las_citas_del_dia_actual(): void
    {
        $citaHoy = $this->crearCita(now()->format('Y-m-d'), '09:00:00', '10:30:00', 'Karen Erazo');
        $this->crearCita(now()->addDay()->format('Y-m-d'), '11:00:00', '12:30:00', 'Cliente mañana');

        $response = $this->withSession($this->sesionAdmin())
            ->get(route('admin.dashboard', ['tab' => 'citas']));

        $response->assertStatus(200);
        $response->assertViewHas('citasHoy', function ($citasHoy) use ($citaHoy) {
            return $citasHoy->count() === 1 && (int) $citasHoy->first()->id_cita === (int) $citaHoy->id_cita;
        });
    }

    /**
     * CID-2
     * Cuando el sistema muestre cada cita del día,
     * entonces cada cita debe incluir cliente, servicio, trabajador y hora.
     */
    public function test_hu05_cid2_cada_cita_del_dia_incluye_cliente_servicio_trabajador_y_hora(): void
    {
        $this->crearCita(now()->format('Y-m-d'), '09:00:00', '10:30:00', 'Karen Erazo');

        $response = $this->withSession($this->sesionAdmin())
            ->get(route('admin.dashboard', ['tab' => 'citas']));

        $response->assertStatus(200);
        $response->assertSee('Karen Erazo');
        $response->assertSee('Maquillaje profesional');
        $response->assertSee('Laura Gómez');
        $response->assertSee('09:00');
    }

    /**
     * CID-3
     * Cuando existan varias citas programadas para el día,
     * entonces las citas deben mostrarse en orden según el horario programado.
     */
    public function test_hu05_cid3_ordena_las_citas_del_dia_por_hora_programada(): void
    {
        $citaTarde = $this->crearCita(now()->format('Y-m-d'), '15:00:00', '16:00:00', 'Cliente tarde');
        $citaManana = $this->crearCita(now()->format('Y-m-d'), '08:00:00', '09:00:00', 'Cliente temprano');

        $response = $this->withSession($this->sesionAdmin())
            ->get(route('admin.dashboard', ['tab' => 'citas']));

        $response->assertStatus(200);
        $response->assertViewHas('citasHoy', function ($citasHoy) use ($citaManana, $citaTarde) {
            return $citasHoy->pluck('id_cita')->values()->all() === [
                $citaManana->id_cita,
                $citaTarde->id_cita,
            ];
        });
    }

    private function crearCita(string $fecha, string $horaInicio, string $horaFin, string $nombreCliente): Cita
    {
        $cita = Cita::create([
            'id_cliente' => $this->cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => $nombreCliente,
            'telefono_contacto' => '3001234567',
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
            'area' => 'Maquillaje',
        ]);

        return $cita;
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
