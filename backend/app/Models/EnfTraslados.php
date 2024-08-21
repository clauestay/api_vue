<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnfTraslados extends Model
{
    use HasFactory;

    // esquema de conexion
    protected $connection = 'SINERGIA';

    // nombre de la tabla
    protected $table = 'HOSINC.ENF_TRASLADOS';

    public static function getTrasladosPaciente($id_ingreso)
    {
        $traslados = self::select('id_traslado','unidad_origen', 'cod_cama', 'unidad_destino', 'cod_pieza', 'fecha_ingreso')
        ->where('id_ingreso', $id_ingreso)
        ->where('unidad_origen', '<>', 8)
        ->where('estado_traslado', '=', 'REC')
        ->orderBy('id_traslado', 'desc')
        // ->first();
        ->take(2)
        ->get();

        $ultimo_traslado = [];

        if ($traslados && count($traslados) == 2) {
            foreach ($traslados as $key => $traslado) {
                $ultimo_traslado = [
                    "cod_unidad_origen" => $traslados[0]->unidad_origen,
                    // "unidad_origen" => GenUnidad::getDescripcionUnidad($traslados[0]->unidad_origen)->desc_unidad,
                    "pieza_origen" => $traslados[1]->cod_pieza,
                    "cama_origen" => $traslados[1]->cod_cama,
                    "cod_unidad_destino" => $traslados[0]->unidad_destino,
                    // "unidad_destino" => GenUnidad::getDescripcionUnidad($traslados[0]->unidad_destino)->desc_unidad,
                    "pieza_destino" => $traslados[0]->cod_pieza,
                    "cama_destino" => $traslados[0]->cod_cama
                ];
            }
        }

        return $ultimo_traslado;
    }
}
