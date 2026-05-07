<?php

namespace Tests\Feature\Sprint4;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GestionAsistenciaCitasTest extends TestCase
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
            'telefono' => '3228302874',
            'contrasena' => bcrypt('password123'),
            'fecha_registro' => now(),
        ]);

        $this->servicio = Servicio::create([
            'nombre_servicio' => 'Pedicure spa',
            'descripcion' => 'Servicio de pedicure con hidratación y limpieza.',
            'precio' => 55000,
            'duracion_minutos' => 60,
            'estado' => 'activo',
            'imagen' => null,
        ]);

        $this->trabajador = Trabajador::create([
            'nombre_completo' => 'Carolina Díaz',
            'especialidad' => 'Pedicure',
            'foto' => null,
            'estado' => 'activo',
        ]);

        DB::table('trabajador_servicio')->insert([
            'id_trabajador' => $this->trabajador->id_trabajador,
            'id_servicio' => $this->servicio->id_servicio,
        ]);
    }

    public function test_hu07_cid1_visualiza_las_citas_pendientes_de_atencion(): void
    {
        $this->crearCita('registrada');

        $response = $this->withSession($this->sesionAdmin())
            ->get(route('admin.dashboard', ['tab' => 'citas']));

        $response->assertStatus(200);
        $response->assertSee('Karen Erazo');
        $response->assertSee('Pedicure spa');
        $response->assertSee('Pendiente');
    }

    public function test_hu07_cid2_marca_la_cita_como_atendida(): void
    {
        $cita = $this->crearCita('registrada');

        $response = $this->withSession($this->sesionAdmin())
            ->patch(route('admin.citas.asistencia', $cita), [
                'estado' => 'completada',
            ]);

        $response->assertSessionHas(
            'success',
            'La cita fue marcada como asistida correctamente.'
        );

        $this->assertDatabaseHas('citas', [
            'id_cita' => $cita->id_cita,
            'estado' => 'completada',
        ]);
    }

    public function test_hu07_cid3_registra_la_inasistencia_del_cliente(): void
    {
        $cita = $this->crearCita('registrada');

        $response = $this->withSession($this->sesionAdmin())
            ->patch(route('admin.citas.asistencia', $cita), [
                'estado' => 'inasistencia',
            ]);

        $response->assertSessionHas(
            'success',
            'La cita fue marcada como no asistida correctamente.'
        );

        $this->assertDatabaseHas('citas', [
            'id_cita' => $cita->id_cita,
            'estado' => 'inasistencia',
        ]);
    }

    public function test_hu07_cid4_refleja_el_estado_final_de_cada_cita(): void
    {
        $citaCompletada = $this->crearCita(
            'completada',
            '09:00:00',
            '10:00:00'
        );

        $citaInasistencia = $this->crearCita(
            'inasistencia',
            '11:00:00',
            '12:00:00'
        );

        $response = $this->withSession($this->sesionAdmin())
            ->get(route('admin.dashboard', ['tab' => 'citas']));

        $response->assertStatus(200);

        $response->assertSee('Sí asistió');
        $response->assertSee('No asistió');

        $this->assertDatabaseHas('citas', [
            'id_cita' => $citaCompletada->id_cita,
            'estado' => 'completada',
        ]);

        $this->assertDatabaseHas('citas', [
            'id_cita' => $citaInasistencia->id_cita,
            'estado' => 'inasistencia',
        ]);
    }

    public function test_hu07_no_permita_registrar_asistencia_en_cita_cancelada(): void
    {
        $cita = $this->crearCita('cancelada');

        $response = $this->withSession($this->sesionAdmin())
            ->patch(route('admin.citas.asistencia', $cita), [
                'estado' => 'completada',
            ]);

        $response->assertSessionHas(
            'error',
            'No se puede registrar asistencia en una cita cancelada.'
        );

        $this->assertDatabaseHas('citas', [
            'id_cita' => $cita->id_cita,
            'estado' => 'cancelada',
        ]);
    }

    private function crearCita(
        string $estado = 'registrada',
        string $horaInicio = '09:00:00',
        string $horaFin = '10:00:00'
    ): Cita {
        $cita = Cita::create([
            'id_cliente' => $this->cliente->id_cliente,
            'id_trabajador' => $this->trabajador->id_trabajador,
            'nombre_cliente' => $this->cliente->nombre_completo,
            'telefono_contacto' => $this->cliente->telefono,
            'fecha_cita' => now()->format('Y-m-d'),
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'duracion_total_minutos' => 60,
            'notas' => null,
            'estado' => $estado,
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
            'area' => 'Pedicure',
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