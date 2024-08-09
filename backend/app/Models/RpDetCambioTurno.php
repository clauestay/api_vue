<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Paciente;
use App\Models\AmFichas;

class RpDetCambioTurno extends Model
{
    use HasFactory;
    // esquema de conexion
    protected $connection = 'GESTION';

    // nombre de la tabla
    protected $table = 'INTRANET.RP_DET_CT_ENTREGADOS';

    // definiendo que esta sera la clave primaria.
    protected $primaryKey = 'id_entregado';

    public $timestamps = false;

    // relaciones


    // funciones

    public static function obtenerIdsEntregadosTurno($id_cambio_turno)
    {
        return self::select("id_entregado")
            ->where("id_cambio_turno", $id_cambio_turno)
            ->where("vigencia", 1)
            ->get();
    }

    // public static function obtenerEntregadosTurno($id_cambio_turno)
    // {
    //     return self::where('ID_CAMBIO_TURNO', $id_cambio_turno)
    //         ->where('VIGENCIA', 1)
    //         ->get();
    // }

    //guardar detalle pacientes entregados al final del turno.
    // public static function guardarEntregados($datos, $id_cambio_turno)
    // {
    //     // Limpiar rut
    //     $dato_rut = $datos['run'];
    //     $rut_limpio = str_replace('.', '', $dato_rut);
    //     $rut_dividido = explode('-', $rut_limpio);
    //     $rut = $rut_dividido[0];
    //     $digito = $rut_dividido[1];

    //     // buscar ficha
    //     $paciente = Paciente::where('RUT_PACIENTE', $rut)->first(['id_ambulatorio']);
    //     $ficha = AmFichas::where('ID_AMBULATORIO', $paciente->id_ambulatorio)->first(['num_ficha']);

    //     $entregados = new RpDetCambioTurno();
    //     $entregados->ID_CAMBIO_TURNO = $id_cambio_turno;
    //     $entregados->FICHA = $ficha['num_ficha'];
    //     $entregados->RUT = $rut;
    //     $entregados->DIGITO = $digito;
    //     $entregados->PROBLEMAS_INTERVENCION = $datos['problemas'];
    //     $entregados->EXAMENES = $datos['examenes'];
    //     $entregados->save();

    //     return $entregados;
    // }

    public static function getEntregadosTurno($id_cambio_turno)
    {
        $entregados = self::select('ID_ENTREGADO', 'ID_CAMBIO_TURNO', 'RUT', 'DIGITO', 'PROBLEMAS_INTERVENCION as problemas', 'EXAMENES')
        ->where('ID_CAMBIO_TURNO', $id_cambio_turno)
        ->where('VIGENCIA', 1)
        ->get();

        return $entregados->map(function ($entregado) {
            $entregado->run = $entregado->rut. '-' .$entregado->digito;
            $entregado->editable = false;
            return $entregado;
        });
    }

    /**
     * manipular datos entrantes.
     * diferenciar registros nuevos y eliminados.
     * insertar los nuevos.
     * dejar no vigente los eliminados.
     */
    public static function actualizarEntregados($datos, $id_cambio_turno)
    {
        // obtener ids de entregados originales.
        $ids_originales = self::obtenerIdsEntregadosTurno($id_cambio_turno);
        $originales = [];
        foreach ($ids_originales as $original) {
            if ($original !== '') {
                $originales[] = $original->id_entregado;
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
                $view_restantes[] = $dato['id_entregado'];
                $view_actualizar[] = $dato;
            }
        }

        // insertar nuevos.
        foreach ($view_agregado as $nuevo) {
            $entregado = self::validarEntregadoRegistrado($nuevo, $id_cambio_turno);
            if ($entregado === false) {
                self::guardarEntregados($nuevo, $id_cambio_turno);
            } else {
                throw new \Exception("El Paciente con run {$nuevo['run']} ya se encuentra como entregado en este turno.");
            }
        }

         // identificar los actualizados.
         foreach ($view_actualizar as $actualizado) {
            self::updateEntregado($actualizado);
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

    public static function dejarNoVigentes($id_entregado)
    {
        return self::where('ID_ENTREGADO', $id_entregado)
            ->update(['VIGENCIA' => 0]);
    }

    public static function validarEntregadoRegistrado($entregado, $id_cambio_turno)
    {
        $validado = self::where('ID_CAMBIO_TURNO', $id_cambio_turno)
        ->where('RUT', $entregado['v_run'])
        ->where('VIGENCIA', 1)
        ->first();

        return $validado ? true : false;
    }

    public static function updateEntregado($actualizado)
    {
        $entregado = self::where('id_entregado', $actualizado['id_entregado'])->first();
        if ($entregado) {
            $entregado->PROBLEMAS_INTERVENCION = $actualizado['problemas'];
            $entregado->EXAMENES = $actualizado['examenes'];
            $entregado->save();
        }

        return $entregado;
    }
}
