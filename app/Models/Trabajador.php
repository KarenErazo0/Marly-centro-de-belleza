<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores';

    protected $primaryKey = 'id_trabajador';

    protected $fillable = [
        'nombre_completo',
        'especialidad',
        'foto',
        'estado',
    ];

    public $timestamps = false;

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class, 'trabajador_servicio', 'id_trabajador', 'id_servicio');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_trabajador', 'id_trabajador');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CitaDetalle::class, 'id_trabajador', 'id_trabajador');
    }
}