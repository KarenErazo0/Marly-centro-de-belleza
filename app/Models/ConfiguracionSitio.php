<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSitio extends Model
{
    protected $table = 'configuracion_sitio';

    protected $fillable = [
        'hero_imagen',
        'contacto_ubicacion',
        'contacto_telefono',
        'contacto_correo',
        'contacto_horario',
        'instagram_url',
        'whatsapp_url',
    ];

    public $timestamps = true;
}
