<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GenUnidad extends Model
{
    use HasFactory;

    // esquema de conexion
    protected $connection = 'SINERGIA';

    // nombre de la tabla
    protected $table = 'GENINC.GEN_UNIDAD';

    public static function getDescripcionUnidad($cod_unidad)
    {
        return self::select('desc_unidad')
        ->where('cod_unidad', $cod_unidad)
        ->first();
    }

    public static function getListadoServiciosHospitalizado()
    {
        return [
            ["cod_unidad" => "24", "desc_unidad" => "CIRUGÍA DAMAS"],
            ["cod_unidad" => "23", "desc_unidad" => "CIRUGÍA VARONES"],
            ["cod_unidad" => "31", "desc_unidad" => "CUIDADOS PALIATIVOS HOSP"],
            ["cod_unidad" => "29", "desc_unidad" => "MEDICINA/ESPECIALIDADES MÉDICAS"],
            ["cod_unidad" => "27", "desc_unidad" => "NEUTROPÉNICO"],
            ["cod_unidad" => "72", "desc_unidad" => "PABELLÓN PRUEBA"],
            ["cod_unidad" => "30", "desc_unidad" => "PENSIONADO"],
            ["cod_unidad" => "28", "desc_unidad" => "QUIMIOTERAPIA HOSPITALIZADO"],
            ["cod_unidad" => "25", "desc_unidad" => "RADIOYODO HOSP"],
            ["cod_unidad" => "32", "desc_unidad" => "RECUPERACIÓN"],
            ["cod_unidad" => "26", "desc_unidad" => "UNIDAD DE INTERMEDIO"],
            ["cod_unidad" => "42", "desc_unidad" => "UNIDAD DE PABELLONES QUIRÚRGICOS"],
            ["cod_unidad" => "171", "desc_unidad" => "UNIDAD DE PROCEDIMIENTOS MÉDICOS Y ENDOSCOPÍA"]
        ];
    }
}
