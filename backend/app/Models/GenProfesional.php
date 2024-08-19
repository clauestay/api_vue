<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GenProfesional extends Model
{
    use HasFactory;
    // esquema de conexion
    protected $connection = 'SINERGIA';

    // nombre de la tabla
    // protected $table = 'GENINC.GEN_PROFESIONAL';
    protected $table = 'GEN_PROFESIONAL';

    // definiendo que esta sera la clave primaria.
    protected $primaryKey = 'cod_prof';

    public function getNombreCompletoAttribute()
    {
        return ($this->attributes['nombre1_prof'] ?? '') . ' ' . ($this->attributes['nombre2_prof'] ?? '') . ' ' . ($this->attributes['apepat_prof'] ?? '') . ' ' . ($this->attributes['apemat_prof']);
    }
}