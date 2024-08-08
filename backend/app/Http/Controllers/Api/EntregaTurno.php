<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CambioTurno\CrearCambioTurnoRequest;
use App\Http\Requests\CambioTurno\BuscarTurnoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;

// MODELOS
use App\Models\User;
use App\Models\RpEquipoStaff;
use App\Models\Paciente;
use App\Models\RpCambioTurno;
use App\Models\EnfTraslados;
use App\Models\AdmIngresos;
use App\Models\RpDetCambioTurno;
use App\Models\RpDetCtTraslados;
use App\Models\RpDetCtFallecidos;
use App\Models\RpDetCtCirugias;
use App\Models\GenUnidad;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon as carbon;
use Illuminate\Support\Facades\DB;
use Debugbar;

class EntregaTurno extends Controller
{
    public function listadoTurnos(Request $request): JsonResponse
    {
        $search = $request->input('search', null);
        $sortColumn = $request->input('sort_column', 'ID_CAMBIO_TURNO');
        $sortDirection = $request->input('sort_direction', 'desc');
        $pageSize = $request->input('pagesize', 10);

        $turnos = RpCambioTurno::getAllTurnos($search, $sortColumn, $sortDirection, $pageSize);

        $turnos->transform(function ($turno) {
            $turno->FECHA_LLEGADA = Carbon::parse($turno->FECHA_LLEGADA)->format('d-m-Y H:i');
            $turno->FECHA_SALIDA = Carbon::parse($turno->FECHA_SALIDA)->format('d-m-Y H:i');
            return $turno;
        });

        return response()->json([
            'turnos' => $turnos,
        ]);
    }

    public function misTurnos(Request $request): JsonResponse
    {
        // obtener rut del usuario logeado.
        $rut = $request->user()->rut;

        // obtener el codigo de profesional del usuario logeado.
        $usuario = User::where('rut', $rut)->with('codigoProfesional')->first();

        if($usuario && $usuario->codigoProfesional?->cod_prof) {
            $cod_prof = $usuario->codigoProfesional->cod_prof;
            $search = $request->input('search', null);
            $pageSize = $request->input('pagesize', 10);
            $misTurnos = RpCambioTurno::getMisTurnos($cod_prof, $search, $pageSize);
            $misTurnos->transform(function ($turno) {
                // formato de fecha
                $turno->fecha_llegada = Carbon::parse($turno->fecha_llegada)->format('d-m-Y H:i');
                $turno->fecha_salida = Carbon::parse($turno->fecha_salida)->format('d-m-Y H:i');
                return $turno;
            });

            return response()->json([
                "turnos" => $misTurnos,

            ]);
        } else {
            return response()->json([
                "error" => "No tiene asignado un Código de Profesional."
            ], 400);
        }
    }
}