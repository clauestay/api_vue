<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GenUnidad;
use Illuminate\Support\Facades\DB;

class RpDetCtTraslados extends Model
{
    use HasFactory;
    // esquema de conexion
    protected $connection = 'GESTION';

    // nombre de la tabla
    protected $table = 'INTRANET.RP_DET_CT_TRASLADOS';

    // definiendo que esta sera la clave primaria.
    protected $primaryKey = 'id_traslado';

    public $timestamps = false;

    public static function guardarTraslados($datos, $id_cambio_turno)
    {
        // Limpiar rut
        $dato_rut = $datos["run"];
        $rut_limpio = str_replace('.', '', $dato_rut);
        $rut_dividido = explode('-', $rut_limpio);
        $rut = $rut_dividido[0];
        $digito = $rut_dividido[1];

        // Obtener el próximo valor de la secuencia
        // $id_traslado = DB::select("SELECT RP_DET_CT_TRASLADOS_SEQ.NEXTVAL AS nextval FROM dual")[0]->nextval;

        $datalle_traslado = $datos['detalle'];

        $traslado = new RpDetCtTraslados();
        $traslado->ID_CAMBIO_TURNO = $id_cambio_turno;
        $traslado->RUT = $rut;
        $traslado->DIGITO = $digito;
        $traslado->COD_UNIDAD_ORIGEN = $datalle_traslado['cod_unidad_origen'];
        $traslado->PIEZA_ORIGEN = $datalle_traslado['pieza_origen'];
        $traslado->CAMA_ORIGEN = $datalle_traslado['cama_origen'];
        $traslado->COD_UNIDAD_DESTINO = $datalle_traslado['cod_unidad_destino'];
        $traslado->PIEZA_DESTINO = $datalle_traslado['pieza_destino'];
        $traslado->CAMA_DESTINO = $datalle_traslado['cama_destino'];
        $traslado->VIGENCIA = '1';
        $traslado->save();

        return $traslado;
    }

    public static function obtenerIdsTrasladosTurno($id_cambio_turno)
    {
        return self::select("id_traslado")
            ->where("id_cambio_turno", $id_cambio_turno)
            ->where("vigencia", 1)
            ->get();
    }

    public static function getTrasladosTurno($id_cambio_turno)
    {
        $traslados = self::where('ID_CAMBIO_TURNO', $id_cambio_turno)
            ->where('VIGENCIA', 1)
            ->get();

        return $traslados->map(function ($traslado) {
            return
                [
                    'id_traslado' => $traslado->id_traslado,
                    'id_cambio_turno' => $traslado->id_cambio_turno,
                    'rut' => $traslado->rut,
                    'run' => $traslado->rut . '-' . $traslado->digito,
                    'detalle' => [
                        'cod_unidad_origen' => $traslado->cod_unidad_origen,
                        'pieza_origen' => $traslado->pieza_origen,
                        'cama_origen' => $traslado->cama_origen,
                        'cod_unidad_destino' => $traslado->cod_unidad_destino,
                        'pieza_destino' => $traslado->pieza_destino,
                        'cama_destino' => $traslado->cama_destino
                    ],
                    'editable' => false
                ];
        });
    }

    public static function actualizarTraslados($datos, $id_cambio_turno)
    {
        $ids_originales = self::obtenerIdsTrasladosTurno($id_cambio_turno);
        $originales = [];
        foreach ($ids_originales as $original) {
            if ($original !== '') {
                $originales[] = $original->id_traslado;
            }
        }

        $view_agregado = []; // datos que agregaron.
        $view_restantes = []; // datos que permanecieron en la vista.
        $view_actualizar = []; // datos que actualizaron en la vista.

        foreach ($datos as $dato) {
            // almacenar registros nuevos.
            if ($dato['editable'] === true) {
                $view_agregado[] = $dato;
            } else {
                // almacenar registros que permanecieron. (no eliminados en la vista)
                $view_restantes[] = $dato['id_traslado'];
                $view_actualizar[] = $dato;
            }
        }

        // insertar nuevos.
        foreach ($view_agregado as $nuevo) {
            $traslado = self::validarTrasladoRegistrado($nuevo, $id_cambio_turno);
            if ($traslado === false) {
                self::guardarTraslados($nuevo, $id_cambio_turno);
            } else {
                throw new \Exception("El Paciente con run {$nuevo['run']} ya se encuentra como trasladado en este turno.");
            }
        }

        // identificar los actualizados.
        foreach ($view_actualizar as $actualizado) {
            self::updateTraslado($actualizado);
        }

        // identificar los eliminados.
        $quitar = array_diff($originales, $view_restantes);

        // falsear los registros eliminados en vista.
        foreach ($quitar as $quitar_id) {
            if ($quitar_id) {
                self::dejarNoVigentes($quitar_id);
            }
        }
    }

    public static function dejarNoVigentes($id_traslado)
    {
        return self::where('id_traslado', $id_traslado)
            ->update(['VIGENCIA' => 0]);
    }

    public static function validarTrasladoRegistrado($traslado, $id_cambio_turno)
    {
        $validado = self::where('ID_CAMBIO_TURNO', $id_cambio_turno)
            ->where('RUT', $traslado['v_run'])
            ->where('VIGENCIA', 1)
            ->first();

        return $validado ? true : false;
    }

    public static function updateTraslado($actualizado)
    {
        $traslado = self::where('id_traslado', $actualizado['id_traslado'])->first();
        $detalle = $actualizado['detalle'];
        if ($traslado) {
            $traslado->cod_unidad_origen = $detalle['cod_unidad_origen'];
            $traslado->pieza_origen = $detalle['pieza_origen'];
            $traslado->cama_origen = $detalle['cama_origen'];
            $traslado->cod_unidad_destino = $detalle['cod_unidad_destino'];
            $traslado->pieza_destino = $detalle['pieza_destino'];
            $traslado->cama_destino = $detalle['cama_destino'];
            $traslado->save();
        }

        return $traslado;
    }

    public static function getDescripcionUnidadPorCodigo($cod_unidad)
    {
        $unidades = GenUnidad::getListadoServiciosHospitalizado();

        foreach ($unidades as $unidad) {
            if ($unidad['cod_unidad'] == $cod_unidad) {
                return $unidad['desc_unidad'];
            }
        }

        return null; // O un mensaje de error, dependiendo de lo que quieras hacer si no se encuentra la unidad
    }
}
