<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $primaryKey = 'id_servicio';

    protected $fillable = [
        'nombre_servicio',
        'descripcion',
        'precio',
        'duracion_minutos',
        'estado',
        'imagen',
    ];

    public $timestamps = false;


    public function trabajadores(): BelongsToMany
    {
        return $this->belongsToMany(Trabajador::class, 'trabajador_servicio', 'id_servicio', 'id_trabajador');
    }

    public function citas(): BelongsToMany
    {
        return $this->belongsToMany(Cita::class, 'cita_servicio', 'id_servicio', 'id_cita');
    }
}

