<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $primaryKey = 'id_cita';

    protected $fillable = [
        'id_cliente',
        'id_trabajador',
        'nombre_cliente',
        'telefono_contacto',
        'fecha_cita',
        'hora_inicio',
        'hora_fin',
        'duracion_total_minutos',
        'notas',
        'estado',
        'fecha_registro',
    ];

    public $timestamps = false;

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'id_trabajador', 'id_trabajador');
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class, 'cita_servicio', 'id_cita', 'id_servicio');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CitaDetalle::class, 'id_cita', 'id_cita');
    }
}
