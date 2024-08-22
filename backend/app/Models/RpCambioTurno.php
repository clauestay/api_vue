<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
// use App\Models\GenProfesional;
use App\Models\RpEquipoStaff;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RpCambioTurno extends Model
{
    use HasFactory;
    // esquema de conexion
    protected $connection = 'GESTION';

    // nombre de la tabla
    protected $table = 'INTRANET.RP_ENTREGA_TURNO';


    // definiendo que esta sera la clave primaria.
    protected $primaryKey = 'id_cambio_turno';

    public $timestamps = false;

    public function getFechaAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    // relaciones
    public function medicoEntrega()
    {
        return $this->belongsTo(RpEquipoStaff::class, 'doctor_entrega_turno', 'cod_prof')
            ->where('sta_vigencia', 'S')
            ->select('cod_prof', 'sta_descripcion');
        // return $this->belongsTo(GenProfesional::class, 'doctor_entrega_turno', 'cod_prof')
        //     ->where('vigencia', 'S')
        //     ->addSelect([
        //         'cod_prof',
        //         'nombre1_prof',
        //         'nombre2_prof',
        //         'apepat_prof',
        //         'apemat_prof',
        //         'vigencia',
        //         DB::raw("NVL(nombre1_prof, '') || ' ' || NVL(nombre2_prof, '') || ' ' || NVL(apepat_prof, '') || ' ' || NVL(apemat_prof, '') as nombre_completo")
        //     ]);
    }

    public function medicoRecibe()
    {
        return $this->belongsTo(RpEquipoStaff::class, 'doctor_recibe_turno', 'cod_prof')
        ->where('sta_vigencia', 'S')
        ->select('cod_prof', 'sta_descripcion');
        // return $this->belongsTo(GenProfesional::class, 'doctor_recibe_turno', 'cod_prof')
        //     // ->select('cod_prof AS id', 'nombre_completo AS name')
        //     ->where('vigencia', 'S')
        //     ->addSelect([
        //         'cod_prof',
        //         'nombre1_prof',
        //         'nombre2_prof',
        //         'apepat_prof',
        //         'apemat_prof',
        //         'vigencia',
        //         DB::raw("NVL(nombre1_prof, '') || ' ' || NVL(nombre2_prof, '') || ' ' || NVL(apepat_prof, '') || ' ' || NVL(apemat_prof, '') as nombre_completo")
        //     ]);
    }

    public static function getMisTurnos($cod_prof, $search = null)
    {
        $query = self::select('ID_CAMBIO_TURNO', 'FECHA_LLEGADA', 'FECHA', 'FECHA_SALIDA', 'DOCTOR_ENTREGA_TURNO', 'DOCTOR_RECIBE_TURNO')
            ->where('DOCTOR_ENTREGA_TURNO', $cod_prof)
            ->where('ESTADO', 1)
            ->with(['medicoEntrega', 'medicoRecibe'])
            ->orderBy('ID_CAMBIO_TURNO', 'DESC');

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->where('FECHA_LLEGADA', 'like', "%{$search}%")
                    ->orWhere('FECHA_SALIDA', 'like', "%{$search}%")
                    ->orWhereHas('medicoEntrega', function ($qe) use ($search) {
                        $qe->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(NOMBRE1_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(NOMBRE2_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(APEPAT_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(APEMAT_PROF) like ?', ["%{$search}%"]);
                        });
                    })
                    ->orWhereHas('medicoRecibe', function ($qr) use ($search) {
                        $qr->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(NOMBRE1_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(NOMBRE2_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(APEPAT_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(APEMAT_PROF) like ?', ["%{$search}%"]);
                        });
                    });
            });
        }

        return $query->paginate()->onEachSide(2);
    }

    // funciones
    public static function getAllTurnos($search = null )
    {
        $query = self::select('ID_CAMBIO_TURNO', 'FECHA_LLEGADA', 'FECHA', 'FECHA_SALIDA', 'DOCTOR_ENTREGA_TURNO', 'DOCTOR_RECIBE_TURNO')
            ->where('ESTADO', 1)
            ->with(['medicoEntrega', 'medicoRecibe'])
            ->orderBy('ID_CAMBIO_TURNO', 'DESC');
            // ->orderBy($sortColumn, $sortDirection);

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->where('FECHA_LLEGADA', 'like', "%{$search}%")
                    ->orWhere('FECHA_SALIDA', 'like', "%{$search}%")
                    ->orWhereHas('medicoEntrega', function ($qe) use ($search) {
                        $qe->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(NOMBRE1_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(NOMBRE2_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(APEPAT_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(APEMAT_PROF) like ?', ["%{$search}%"]);
                        });
                    })
                    ->orWhereHas('medicoRecibe', function ($qr) use ($search) {
                        $qr->where(function ($query) use ($search) {
                            $query->whereRaw('LOWER(NOMBRE1_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(NOMBRE2_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(APEPAT_PROF) like ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(APEMAT_PROF) like ?', ["%{$search}%"]);
                        });
                    });
            });
        }

        return $query->paginate()->onEachSide(2);
    }

    public static function getAllTurnosDoctor($cod_prof)
    {
        // retorna todos los turnos
        return self::select('TA.ID_CAMBIO_TURNO', 'TA.FECHA', 'TA.DOCTOR_ENTREGA_TURNO', 'TA.DOCTOR_RECIBE_TURNO')
            ->where('TA.ID_CAMBIO_TURNO', '>', 513)
            ->where('TA.DOCTOR_ENTREGA_TURNO', '=', $cod_prof)
            // retorna las relaciones
            ->with(['medicoEntrega', 'medicoRecibe'])
            ->orderBy('TA.ID_CAMBIO_TURNO', 'DESC')
            ->paginate(10);
    }

    public static function getTurno($id_cambio_turno)
    {
        // retorna todos los turnos
        return self::where('ID_CAMBIO_TURNO', '=', $id_cambio_turno)
            ->where('ESTADO', 1)
            // retorna las relaciones
            ->with(['medicoEntrega', 'medicoRecibe'])
            ->first();
    }

    public static function getDetalleTurno($id_cambio_turno)
    {
        // retorna turno especifico
        $detalles = self::select('TB.ID_DET_CAMBIO_TURNO', 'TA.ID_CAMBIO_TURNO', 'TD.RUT_PACIENTE', 'DV_PACIENTE', 'NOMBRE_PACIENTE', 'APEMAT_PACIENTE', 'APEPAT_PACIENTE', 'TC.ID_AMBULATORIO', 'TB.PROBLEMAS_INTERVENCION as problemas', 'TB.EXAMENES')
            ->leftjoin('RP_DET_CAMBIO_TURNO TB', 'TA.ID_CAMBIO_TURNO', '=', 'TB.ID_CAMBIO_TURNO')
            ->leftjoin('AM_FICHAS TC', 'TC.NUM_FICHA', '=', 'TB.FICHA')
            ->leftjoin('TAB_PACIENTE TD', 'TC.ID_AMBULATORIO', '=', 'TD.ID_AMBULATORIO')
            ->where('TA.ID_CAMBIO_TURNO', '>', 513)
            ->where('TA.ID_CAMBIO_TURNO', '=', $id_cambio_turno)
            ->where('TB.VIGENCIA', '1')
            // retorna las relaciones
            // ->with(['medicoEntrega', 'medicoRecibe'])
            ->orderBy('TA.ID_CAMBIO_TURNO', 'DESC')
            ->get();

        return $detalles->map(function ($detalle) {
            $diagnostico = Paciente::traerDiagnostico($detalle->id_ambulatorio);
            $detalle->run = $detalle->rut_paciente . '-' . $detalle->dv_paciente;
            $detalle->diagnostico = $diagnostico;
            $detalle->editable = false;
            return $detalle;
        });
    }

    // REVISAR
    public static function getDetalleJornadaTurno($jornadas)
    {
        return self::select('TA.ID_JORNADA', 'TA.ID_CAMBIO_TURNO', 'TA.FECHA', 'TA.DOCTOR_ENTREGA_TURNO', 'TA.DOCTOR_RECIBE_TURNO')
            ->where('TA.ID_CAMBIO_TURNO', '>', 513)
            ->whereIn('TA.ID_JORNADA', $jornadas)
            // retorna las relaciones
            ->with(['medicoEntrega', 'medicoRecibe'])
            ->orderBy('TA.ID_CAMBIO_TURNO', 'DESC')
            ->paginate(10);
    }

    public static function compruebaTurno($medico_entrega, $fecha_llegada, $fecha_salida)
    {
        $fecha_llegada = $fecha_llegada ? carbon::parse($fecha_llegada) : null;
        if ($fecha_llegada) {
            $fecha_llegada->format('Y-m-d H:i:s');
        }

        $fecha_salida = $fecha_salida ? carbon::parse($fecha_salida) : null;
        if ($fecha_salida) {
            $fecha_salida->format('Y-m-d H:i:s');
        }

        // return self::select('*')
        //     ->where('DOCTOR_ENTREGA_TURNO', $cod_prof)
        //     ->where('DOCTOR_RECIBE_TURNO', '!=', $cod_prof)
        //     ->where('FECHA_LLEGADA', ">=", $fecha_llegada)
        //     ->where('FECHA_SALIDA', "<=", $fecha_salida)
        //     ->where('ESTADO', 1)
        //     ->orderBy('ID_CAMBIO_TURNO', 'DESC')
        //     ->first();
        return self::select('*')
        ->where('DOCTOR_ENTREGA_TURNO', $medico_entrega)
        ->where(function ($query) use ($fecha_llegada, $fecha_salida) {
            $query->where(function ($q) use ($fecha_llegada) {
                $q->where('FECHA_LLEGADA', '<=', $fecha_llegada)
                  ->where('FECHA_SALIDA', '>=', $fecha_llegada);
            })
            ->orWhere(function ($q) use ($fecha_salida) {
                $q->where('FECHA_LLEGADA', '<=', $fecha_salida)
                  ->where('FECHA_SALIDA', '>=', $fecha_salida);
            })
            ->orWhere(function ($q) use ($fecha_llegada, $fecha_salida) {
                $q->where('FECHA_LLEGADA', '>=', $fecha_llegada)
                  ->where('FECHA_SALIDA', '<=', $fecha_salida);
            });
        })
        ->where('ESTADO', 1)
        ->orderBy('ID_CAMBIO_TURNO', 'DESC')
        ->first();
    }

    // REVISAR
    public static function compruebaTurnoInicio($cod_prof)
    {
        // $fecha = carbon::now()->format('d-m-Y H:i:s');
        // $fecha = Date(strtotime('02-04-2024 00:00:00'));
        $fecha = carbon::now()->format('d-m-Y H:i:s');

        return DB::table('RP_CAMBIO_TURNO AS TA')
            ->select([
                'ID_CAMBIO_TURNO',
                'DOCTOR_ENTREGA_TURNO',
                'DOCTOR_RECIBE_TURNO',
                DB::raw("TO_CHAR(FECHA,'dd/mm/yyyy HH24:MI:SS') AS FECHA"),
                'USUARIO',
                DB::raw("TO_CHAR(HORA_LLEGADA,'dd/mm/yyyy HH24:MI:SS') AS HORA_LLEGADA"),
                DB::raw("TO_CHAR(HORA_SALIDA,'dd/mm/yyyy HH24:MI:SS') AS HORA_SALIDA"),
                'TB.ID_JORNADA',
                'NOVEDADES',
                DB::raw("TO_CHAR(HORA_INICIO_TURNO,'dd/mm/yyyy HH24:MI:SS') AS HORA_INICIO_TURNO"),
                DB::raw("TO_CHAR(HORA_FIN_TURNO,'dd/mm/yyyy HH24:MI:SS') AS HORA_FIN_TURNO"),
                'QHORAS',
                'ESTADO'
            ])
            ->join('RP_JORNADA_TURNO AS TB', 'TA.ID_JORNADA', '=', 'TB.ID_JORNADA')
            ->where('DOCTOR_ENTREGA_TURNO', $cod_prof)
            ->where('ID_CAMBIO_TURNO', function ($query) use ($cod_prof) {
                $query->select(DB::raw('MAX(ID_CAMBIO_TURNO)'))
                    ->from('RP_CAMBIO_TURNO')
                    ->where('DOCTOR_ENTREGA_TURNO', $cod_prof);
            })
            ->whereRaw("TO_DATE(?, 'dd/mm/yyyy HH24:MI:SS') <= TO_DATE(to_char(HORA_FIN_TURNO, 'dd/mm/yyyy HH24:MI:SS'),'dd/mm/yyyy HH24:MI:SS')", [$fecha])
            ->get();
    }

    public static function guardarTurno($datos)
    {
        $fecha_llegada = $datos["fecha_llegada"] ? carbon::parse($datos["fecha_llegada"]) : null;
        if ($fecha_llegada) {
            $fecha_llegada->format('Y-m-d H:i:s');
        }

        $fecha_salida = $datos["fecha_salida"] ? carbon::parse($datos["fecha_salida"]) : null;
        if ($fecha_salida) {
            $fecha_salida->format('Y-m-d H:i:s');
        }

        $cantidad_horas = $fecha_salida->diffInHours($fecha_llegada);

        $turno = new RpCambioTurno();
        $turno->DOCTOR_ENTREGA_TURNO = $datos['medico_entrega']['id'];
        $turno->DOCTOR_RECIBE_TURNO = $datos['medico_recibe']['id'];
        $turno->FECHA = carbon::parse(now())->format('Y-m-d H:i:s');
        $turno->USUARIO = Auth::user()->usuario_id;
        $turno->FECHA_LLEGADA = $fecha_llegada;
        $turno->FECHA_SALIDA = $fecha_salida;
        $turno->QHORAS = $cantidad_horas;
        $turno->NOVEDADES = $datos['novedades'];
        $turno->save();

        return $turno;
    }

    public static function eliminarTurno($id_cambio_turno)
    {
        return self::where('id_cambio_turno', $id_cambio_turno)
            ->update(['ESTADO' => 0]);
    }

    public static function actualizarTurno($datos, $id_cambio_turno)
    {
        $update_turno = self::where('id_cambio_turno', $id_cambio_turno)
            ->where('ESTADO', '1')
            ->first();

        // trabajar los datos
        $fecha_llegada = $datos["fecha_llegada"] ? carbon::parse($datos["fecha_llegada"]) : null;
        if ($fecha_llegada) {
            $fecha_llegada->format('Y-m-d H:i:s');
        }

        $fecha_salida = $datos["fecha_salida"] ? carbon::parse($datos["fecha_salida"]) : null;
        if ($fecha_salida) {
            $fecha_salida->format('Y-m-d H:i:s');
        }

        $update_turno->DOCTOR_ENTREGA_TURNO = $datos['medico_entrega']['id'];
        $update_turno->DOCTOR_RECIBE_TURNO = $datos['medico_recibe']['id'];
        $update_turno->USUARIO = Auth::user()->usuario_id;
        $update_turno->FECHA_LLEGADA = $fecha_llegada;
        $update_turno->FECHA_SALIDA = $fecha_salida;
        $update_turno->NOVEDADES = $datos['novedades'];
        // $update_turno->CANTIDAD_CIRUGIAS = $datos['cantidad_cirugias'];
        // $update_turno->CANTIDAD_FALLECIDOS = $datos['cantidad_fallecidos'];
        // $update_turno->CANTIDAD_TRASLADOS = $datos['cantidad_traslados'];
        $update_turno->save();

        return $update_turno;
    }
}
