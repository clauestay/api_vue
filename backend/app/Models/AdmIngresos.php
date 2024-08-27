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

    // ultimo ingreso como hospitalizado.
    public static function getIdIngreso($id_ambulatorio)
    {
        return self::select('id_ingreso')
        ->where('id_paciente', $id_ambulatorio)
        ->where('cod_tipo_ing', 'HOSP')
        ->orderBy('id_ingreso', 'desc')
        ->first();
    }

    // ultimo ingreso de todos sus ingresos a excepcion de altas y anulaciones.
    public static function getUltimoIngreso($id_ambulatorio)
    {
        $result = self::selectRaw('MAX(id_ingreso) id_ingreso')
        ->where('id_paciente', $id_ambulatorio)
        ->whereNotIn('cod_estado_ingreso', ['ALTA', 'ANU'])
        ->first();

        return $result ? $result->id_ingreso : null;
    }
}
