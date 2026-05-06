<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'google_id',
        'nombre_completo',
        'correo_electronico',
        'google_avatar',
        'telefono',
        'contrasena',
        'fecha_registro',
    ];

    protected $hidden = ['contrasena'];

    public $timestamps = false;

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_cliente', 'id_cliente');
    }
}