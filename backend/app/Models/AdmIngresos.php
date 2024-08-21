<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdmIngresos extends Model
{
    use HasFactory;

    // esquema de conexion
    protected $connection = 'SINERGIA';

    // nombre de la tabla
    protected $table = 'HOSINC.ADM_INGRESOS';

    public static function getIdIngreso($id_ambulatorio)
    {
        return self::select('id_ingreso')
        ->where('id_paciente', $id_ambulatorio)
        ->where('cod_tipo_ing', 'HOSP')
        ->orderBy('id_ingreso', 'desc')
        ->first();
    }
}
