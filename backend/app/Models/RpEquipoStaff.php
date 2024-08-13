<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

class RpEquipoStaff extends Model
{
    use HasFactory;

    // esquema de conexion
    protected $connection = 'GESTION';

    // nombre de la tabla
    protected $table = 'INTRANET.RP_EQUIPO_STAFF';

    public static function getMedicos()
    {
        return DB::table('INTRANET.RP_EQUIPO_STAFF AS TA')
        ->select('TA.COD_PROF AS id','TA.STA_DESCRIPCION AS name')
        ->join('INTRANET.INT_USUARIOS AS TB', 'TA.RUT_PROF', '=', 'TB.RUT')
        ->join('INTRANET.RP_CAMBIO_TURNO_EQUIPOS AS TC', 'TB.USUARIO_ID', '=', 'TC.ID_USUARIO')
        ->where('TA.STA_VIGENCIA', "S")
        ->where('TC.ID_EQUIPO', '1')
        ->get();
    }

    public static function getMedicosByCodProf($cod_prof)
    {
        return DB::table('INTRANET.RP_EQUIPO_STAFF AS TA')
        ->select('TA.COD_PROF AS id','TA.STA_DESCRIPCION AS name')
        ->join('INTRANET.INT_USUARIOS AS TB', 'TA.RUT_PROF', '=', 'TB.RUT')
        ->join('INTRANET.RP_CAMBIO_TURNO_EQUIPOS AS TC', 'TB.USUARIO_ID', '=', 'TC.ID_USUARIO')
        ->where('TA.STA_VIGENCIA', "S")
        ->where('TC.ID_EQUIPO', '1')
        ->where('TA.COD_PROF', $cod_prof)
        ->first();
    }

    public static function getCirugiasMedico($cod_prof, $inicio, $fin) {
        return DB::table('rp_equipo_staff a')
            ->select('a.cod_prof', 'a.sta_codigo_staff', 'a.equ_id_equipo', 'b.id_protocolooper', 'b.fecha_ing', 'c.rut_paciente', 'c.dv_paciente', 'c.intervencion_programada', 'c.hora_inicio_cirugia', 'c.hora_termino_cirugia')
            ->join('rp_det_staff_protoper b', function (JoinClause $join) {
                $join->on('a.sta_codigo_staff', '=', 'b.sta_codigo_staff')
                ->on('a.equ_id_equipo', '=', 'b.equ_id_equipo');
            })
            ->join('rp_enfermeria_pabellon c','b.id_protocolooper','=','c.id_protocolo')
            ->join('tab_paciente d','c.rut_paciente','=','d.rut_paciente')
            ->where('a.cod_prof', $cod_prof)
            ->whereBetween('b.fecha_ing', [$inicio, $fin])
            ->orderByDesc('c.hora_inicio_cirugia')
            // ->paginate(5);
            ->get();
        }
    }
