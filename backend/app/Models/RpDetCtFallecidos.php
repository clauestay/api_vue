<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon as carbon;

class RpDetCtFallecidos extends Model
{
    use HasFactory;
    // esquema de conexion
    protected $connection = 'GESTION';

    // nombre de la tabla
    protected $table = 'INTRANET.RP_DET_CT_FALLECIDOS';

    // definiendo que esta sera la clave primaria.
    protected $primaryKey = 'id_fallecido';

    public $timestamps = false;

    public static function guardarFallecidos($datos, $id_cambio_turno)
    {
        // Limpiar rut
        $dato_rut = $datos["run"];
        $rut_limpio = str_replace('.', '', $dato_rut);
        $rut_dividido = explode('-', $rut_limpio);
        $rut = $rut_dividido[0];
        $digito = $rut_dividido[1];

        // Obtener el próximo valor de la secuencia
        // $id_fallecido = DB::select("SELECT RP_DET_CT_FALLECIDOS_SEQ.NEXTVAL AS nextval FROM dual")[0]->nextval;

        $fallecido = new RpDetCtFallecidos();
        $fallecido->ID_CAMBIO_TURNO = $id_cambio_turno;
        $fallecido->RUT = $rut;
        $fallecido->DIGITO = $digito;
        $fallecido->fecha_fallecido = carbon::parse($datos['fecha_fallecido']);
        $fallecido->VIGENCIA = '1';
        $fallecido->save();

        return $fallecido;
    }

    public static function obtenerIdsFallecidosTurno($id_cambio_turno)
    {
        return self::select("id_fallecido")
            ->where("id_cambio_turno", $id_cambio_turno)
            ->where("vigencia", 1)
            ->get();
    }

    public static function getFallecidosTurno($id_cambio_turno) {
        $fallecidos = self::where('ID_CAMBIO_TURNO', $id_cambio_turno)
        ->where('VIGENCIA', 1)
        ->get();

        return $fallecidos->map(function ($fallecido) {
            $fallecido->run = $fallecido->rut . '-' . $fallecido->digito;
            $fallecido->editable = false;
            return $fallecido;
        });
    }

    public static function actualizarFallecidos($datos, $id_cambio_turno)
    {
        $ids_originales = self::obtenerIdsFallecidosTurno($id_cambio_turno);
        $originales = [];
        foreach ($ids_originales as $original) {
            if ($original !== '') {
                $originales[] = $original->id_fallecido;
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
                $view_restantes[] = $dato['id_fallecido'];
                $view_actualizar[] = $dato;
            }
        }

        // insertar nuevos.
        foreach ($view_agregado as $nuevo) {
            $fallecido = self::validarFallecidoRegistrado($nuevo, $id_cambio_turno);
            if ($fallecido === false) {
                self::guardarFallecidos($nuevo, $id_cambio_turno);
            } else {
                throw new \Exception("El Paciente con run {$nuevo['run']} ya se encuentra como trasladado en este turno.");
            }
        }

         // identificar los actualizados.
         foreach ($view_actualizar as $actualizado) {
            self::updateFallecido($actualizado);
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
        return self::where('id_fallecido', $id_fallecido)
            ->update(['VIGENCIA' => 0]);
    }

    public static function validarFallecidoRegistrado($fallecido, $id_cambio_turno)
    {
        $validado = self::where('ID_CAMBIO_TURNO', $id_cambio_turno)
        ->where('RUT', $fallecido['v_run'])
        ->where('VIGENCIA', 1)
        ->first();

        return $validado ? true : false;
    }

    public static function updateFallecido($actualizado)
    {
        $fallecido = self::where('id_fallecido', $actualizado['id_fallecido'])->first();
        if ($fallecido) {
            $fallecido->fecha_fallecido = carbon::parse($actualizado['fecha_fallecido']);
            $fallecido->save();
        }

        return $fallecido;
    }
}
