<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon as carbon;

class RpDetCtCirugias extends Model
{
    use HasFactory;
    // esquema de conexion
    protected $connection = 'GESTION';

    // nombre de la tabla
    protected $table = 'INTRANET.RP_DET_CT_CIRUGIAS';

    // definiendo que esta sera la clave primaria.
    protected $primaryKey = 'id_cirugia';

    public $timestamps = false;

    public static function guardarCirugias($datos, $id_cambio_turno)
    {
        // Limpiar rut
        $dato_rut = $datos["run"];
        $rut_limpio = str_replace('.', '', $dato_rut);
        $rut_dividido = explode('-', $rut_limpio);
        $rut = $rut_dividido[0];
        $digito = $rut_dividido[1];

        $cirugia = new RpDetCtCirugias();
        $cirugia->ID_CAMBIO_TURNO = $id_cambio_turno;
        $cirugia->RUT = $rut;
        $cirugia->DIGITO = $digito;
        $cirugia->INTERVENCION = $datos['intervencion'];
        $cirugia->FECHA_INICIO = carbon::parse($datos['fecha_inicio']);
        $cirugia->FECHA_FIN = carbon::parse($datos['fecha_fin']);
        $cirugia->VIGENCIA = '1';
        $cirugia->save();

        return $cirugia;
    }

    public static function obtenerIdsCirugiasTurno($id_cambio_turno)
    {
        return self::select("id_cirugia")
            ->where("id_cambio_turno", $id_cambio_turno)
            ->where("vigencia", 1)
            ->get();
    }

    public static function getCirugiasTurno($id_cambio_turno) {
        $fallecidos = self::where('ID_CAMBIO_TURNO', $id_cambio_turno)
        ->where('VIGENCIA', 1)
        ->get();

        return $fallecidos->map(function ($cirugia) {
            $cirugia->run = $cirugia->rut . '-' . $cirugia->digito;
            $cirugia->editable = false;
            return $cirugia;
        });
    }

    public static function actualizarCirugias($datos, $id_cambio_turno)
    {
        $ids_originales = self::obtenerIdsCirugiasTurno($id_cambio_turno);
        $originales = [];
        foreach ($ids_originales as $original) {
            if ($original !== '') {
                $originales[] = $original->id_cirugia;
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
                $view_restantes[] = $dato['id_cirugia'];
                $view_actualizar[] = $dato;
            }
        }

        // insertar nuevos.
        foreach ($view_agregado as $nuevo) {
            $cirugia = self::validarCirugiaRegistrado($nuevo, $id_cambio_turno);
            if ($cirugia === false) {
                self::guardarCirugias($nuevo, $id_cambio_turno);
            } else {
                throw new \Exception("El Paciente con run {$nuevo['run']} ya se encuentra con cirugia en este turno.");
            }
        }

         // identificar los actualizados.
         foreach ($view_actualizar as $actualizado) {
            self::updateCirugia($actualizado);
        }

        // identificar los eliminados.
        $quitar = array_diff($originales, $view_restantes);

        // falsear los registros eliminados en vista.
        foreach($quitar as $quitar_id) {
            if ($quitar_id) {
                self::dejarNoVigentes($quitar_id);
            }
        }
    }

    public static function dejarNoVigentes($id_fallecido)
    {
        return self::where('id_cirugia', $id_fallecido)
            ->update(['VIGENCIA' => 0]);
    }

    public static function validarCirugiaRegistrado($cirugia, $id_cambio_turno)
    {
        $validado = self::where('ID_CAMBIO_TURNO', $id_cambio_turno)
        ->where('RUT', $cirugia['v_run'])
        ->where('VIGENCIA', 1)
        ->first();

        return $validado ? true : false;
    }

    public static function updateCirugia($actualizado)
    {
        $cirugia = self::where('id_cirugia', $actualizado['id_cirugia'])->first();
        if ($cirugia) {
            $cirugia->intervencion = $actualizado['intervencion'];
            $cirugia->fecha_inicio = carbon::parse($actualizado['fecha_inicio']);
            $cirugia->fecha_fin = carbon::parse($actualizado['fecha_fin']);
            $cirugia->save();
        }

        return $cirugia;
    }
}
