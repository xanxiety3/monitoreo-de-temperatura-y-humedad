<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroTemperatura extends Model
{
    protected $table = "registros_temperatura";
    protected $fillable = [
        'laboratorio_id',
        'user_id',
        'tipo',
        'valor_original',
        'valor_corregido',
    ];
}
