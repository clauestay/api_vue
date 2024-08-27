<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Models\AdmIngresos;

class EnfTraslados extends Model
{
    use HasFactory;

    // esquema de conexion
    protected $connection = 'SINERGIA';

    // nombre de la tabla
    protected $table = 'HOSINC.ENF_TRASLADOS';
    protected $primaryKey = 'id_traslado';

    public static function getTrasladosPaciente($id_ingreso)
    {
        $traslados = self::select('id_traslado','unidad_origen', 'cod_cama', 'unidad_destino', 'cod_pieza', 'fecha_ingreso')
        ->where('id_ingreso', $id_ingreso)
        ->where('unidad_origen', '<>', 8)
        ->where('estado_traslado', '=', 'REC')
        ->orderBy('id_traslado', 'desc')
        ->take(2)
        ->get();

        if ($traslados && count($traslados) == 2) {
            return [
                "cod_unidad_origen" => $traslados[0]->unidad_origen,
                "unidad_origen" => GenUnidad::getDescripcionUnidad($traslados[0]->unidad_origen)->desc_unidad,
                "pieza_origen" => $traslados[1]->cod_pieza,
                "cama_origen" => $traslados[1]->cod_cama,
                "cod_unidad_destino" => $traslados[0]->unidad_destino,
                "unidad_destino" => GenUnidad::getDescripcionUnidad($traslados[0]->unidad_destino)->desc_unidad,
                "pieza_destino" => $traslados[0]->cod_pieza,
                "cama_destino" => $traslados[0]->cod_cama
            ];
        } else {
            return collect();
        }
    }

    public static function getDatosHospitalizado($id_ambulatorio)
    {
        $ultimo_ingreso_vigente = AdmIngresos::getUltimoIngreso($id_ambulatorio);

        if (!$ultimo_ingreso_vigente) {
            return collect();
        } else {
            $ultima_ubicacion = DB::connection('SINERGIA')
            ->table('HOSINC.enf_traslados as b')
            ->join('GENINC.gen_unidad as g', 'b.UNIDAD_DESTINO', '=', 'g.cod_unidad')
            ->select('b.UNIDAD_DESTINO as cod_unidad', 'b.COD_PIEZA', 'b.COD_CAMA', 'g.DESC_UNIDAD as destino', 'b.ID_INGRESO')
            ->whereIn('b.id_traslado', function($query) use ($ultimo_ingreso_vigente) {
                $query->select(DB::raw('MAX(id_traslado)'))
                    ->from('HOSINC.enf_traslados')
                    ->where('id_ingreso', $ultimo_ingreso_vigente)
                    ->where('traslado_vigente', 'S');
            })
            ->get();
        }

        return $ultima_ubicacion;
    }
}
