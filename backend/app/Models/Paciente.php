<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Paciente extends Model
{
    use HasFactory;

    // esquema de conexion
    protected $connection = 'SINERGIA';

    // nombre de la tabla
    protected $table = 'GENINC.TAB_PACIENTE';

    protected $primaryKey = 'id_paciente';

    // esto es cuando la tabla no tiene columnas de updated_at y created_at
    public $timestamps = false;

    public function getNombreCompletoAttribute()
    {
        return ($this->attributes['nombre_paciente'] ?? '') . ' ' . ($this->attributes['apepat_paciente'] ?? '') . ' ' . ($this->attributes['apemat_paciente'] ?? '');
    }

    public static function datosPacienteRut($run)
    {
        return self::select('id_ambulatorio', 'nombre_paciente', 'apepat_paciente', 'apemat_paciente')
               ->where('rut_paciente', $run)
               ->first();
    }

    public static function traerDiagnostico($idAmbulatorio)
    {
        $resultado = DB::select("SELECT FNC_TRAEDIAGNOSTICOCOMITEPACTE(:parametro) AS diagnostico FROM DUAL", ['parametro' => $idAmbulatorio]);

        return $resultado[0]->diagnostico;
    }
}
