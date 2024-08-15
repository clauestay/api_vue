<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

// MODELOS
use App\Models\User;
use App\Models\Paciente;
use App\Models\RpCambioTurno;
use App\Models\RpDetCambioTurno;
use App\Models\RpDetCtTraslados;
use App\Models\RpDetCtFallecidos;
use App\Models\RpDetCtCirugias;
use App\Models\RpEquipoStaff;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon as carbon;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\CrearCambioTurnoRequest;

class EntregaTurno extends Controller
{
    public function medicoEntregaTurno($cod_prof): JsonResponse
    {
        $medico_entrega = RpEquipoStaff::getMedicosByCodProf($cod_prof);

        return response()->json([
            'medico_entrega' => $medico_entrega
        ]);
    }

    public function medicosEntregaTurno(Request $request): JsonResponse
    {
        $medicos = RpEquipoStaff::getMedicos();

        return response()->json([
            'medicos' => $medicos
        ]);
    }

    public function obtenerInfoPaciente($rut): JsonResponse
    {
        $paciente = Paciente::datosPacienteRut($rut);
        $datos = null;

        if ($paciente) {
            $diagnostico = Paciente::traerDiagnostico($paciente->id_ambulatorio);
            $datos = [
                "nombre_completo" => $paciente->nombre_completo,
                "diagnostico" => $diagnostico
            ];
        }

        return response()->json(['info_paciente' => $datos]);
    }

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

    public function obtenerTurno($id): JsonResponse
    {
        if (!$id) {
            return response()->json([
                "error" => "Hubo un error con el identificador del turno."
            ], 400);
        }

        $turno = RpCambioTurno::getTurno($id);
        if (!$turno) {
            return response()->json([
                "error" => "No se pudo encontrar el turno."
            ], 400);
        }

        return response()->json([
            "turno" => $turno
        ]);
    }

    public function obtenerEntregados($id): JsonResponse
    {
        if (!$id) {
            return response()->json([
                "error" => "Hubo un error con el identificador del turno."
            ], 400);
        }

        $entregados = RpDetCambioTurno::getEntregadosTurno($id);
        if (!$entregados) {
            return response()->json([
                "error" => "No se pudo encontrar los pacientes entregados."
            ], 400);
        }

        $entregados->map(function ($entregado) {
            $paciente = Paciente::datosPacienteRut($entregado->rut);
            $entregado->nombre_completo = $paciente->nombre_completo;
            $entregado->diagnostico = Paciente::traerDiagnostico($paciente->id_ambulatorio);
            return $entregado;
        });

        return response()->json([
            "entregados" => $entregados
        ]);
    }

    public function obtenerTraslados($id): JsonResponse
    {
        if (!$id) {
            return response()->json([
                "error" => "Hubo un error con el identificador del turno."
            ], 400);
        }

        $traslados = RpDetCtTraslados::getTrasladosTurno($id);
        if (!$traslados) {
            return response()->json([
                "error" => "No se pudo encontrar los pacientes trasladados."
            ], 400);
        }

        $traslados = $traslados->map(function ($traslado) {
            $paciente = Paciente::datosPacienteRut($traslado['rut']);
            $traslado['nombre_completo'] = $paciente->nombre_completo;
            $traslado['diagnostico'] = Paciente::traerDiagnostico($paciente->id_ambulatorio);
            $detalle = $traslado['detalle'];
            $detalle['cod_unidad_origen'] = RpDetCtTraslados::getDescripcionUnidadPorCodigo($detalle['cod_unidad_origen']);
            $detalle['cod_unidad_destino'] = RpDetCtTraslados::getDescripcionUnidadPorCodigo($detalle['cod_unidad_destino']);
            $traslado['detalle'] = $detalle;
            return $traslado;
        });

        return response()->json([
            "traslados" => $traslados
        ]);
    }

    public function obtenerFallecidos($id): JsonResponse
    {
        if (!$id) {
            return response()->json([
                "error" => "Hubo un error con el identificador del turno."
            ], 400);
        }

        $fallecidos = RpDetCtFallecidos::getFallecidosTurno($id);
        if (!$fallecidos) {
            return response()->json([
                "error" => "No se pudo encontrar los pacientes fallecidos."
            ], 400);
        }

        $fallecidos->map(function ($fallecido) {
            $paciente = Paciente::datosPacienteRut($fallecido['rut']);
            $fallecido['nombre_completo'] = $paciente->nombre_completo;
            $fallecido['diagnostico'] = Paciente::traerDiagnostico($paciente->id_ambulatorio);
            return $fallecido;
        });

        return response()->json([
            "fallecidos" => $fallecidos
        ]);
    }

    public function obtenerCirugias($id): JsonResponse
    {
        if (!$id) {
            return response()->json([
                "error" => "Hubo un error con el identificador del turno."
            ], 400);
        }

        $cirugias = RpDetCtCirugias::getCirugiasTurno($id);
        if (!$cirugias) {
            return response()->json([
                "error" => "No se pudo encontrar los pacientes cirugias."
            ], 400);
        }

        $cirugias = RpDetCtCirugias::getCirugiasTurno($id);
        $cirugias->map(function ($cirugia) {
            $paciente = Paciente::datosPacienteRut($cirugia['rut']);
            $cirugia['nombre_completo'] = $paciente->nombre_completo;
            $cirugia['diagnostico'] = Paciente::traerDiagnostico($paciente->id_ambulatorio);
            return $cirugia;
        });

        return response()->json([
            "cirugias" => $cirugias
        ]);
    }

    public function generarPdfTurno($id) //: BinaryFileResponse
    {
        $turno = RpCambioTurno::getTurno($id);

        $entregados = RpDetCambioTurno::getEntregadosTurno($id);
        $entregados->map(function ($entregado) {
            $paciente = Paciente::datosPacienteRut($entregado->rut);
            $entregado->nombre_completo = $paciente->nombre_completo;
            $entregado->diagnostico = Paciente::traerDiagnostico($paciente->id_ambulatorio);
            return $entregado;
        });

        $traslados = RpDetCtTraslados::getTrasladosTurno($id);
        $traslados = $traslados->map(function ($traslado) {
            $paciente = Paciente::datosPacienteRut($traslado['rut']);
            $traslado['nombre_completo'] = $paciente->nombre_completo;
            $traslado['diagnostico'] = Paciente::traerDiagnostico($paciente->id_ambulatorio);
            $detalle = $traslado['detalle'];
            $detalle['cod_unidad_origen'] = RpDetCtTraslados::getDescripcionUnidadPorCodigo($detalle['cod_unidad_origen']);
            $detalle['cod_unidad_destino'] = RpDetCtTraslados::getDescripcionUnidadPorCodigo($detalle['cod_unidad_destino']);
            $traslado['detalle'] = $detalle;
            return $traslado;
        });

        $fallecidos = RpDetCtFallecidos::getFallecidosTurno($id);
        $fallecidos->map(function ($fallecido) {
            $paciente = Paciente::datosPacienteRut($fallecido['rut']);
            $fallecido['nombre_completo'] = $paciente->nombre_completo;
            $fallecido['diagnostico'] = Paciente::traerDiagnostico($paciente->id_ambulatorio);
            return $fallecido;
        });

        $cirugias = RpDetCtCirugias::getCirugiasTurno($id);
        $cirugias->map(function ($cirugia) {
            $paciente = Paciente::datosPacienteRut($cirugia['rut']);
            $cirugia['nombre_completo'] = $paciente->nombre_completo;
            $cirugia['diagnostico'] = Paciente::traerDiagnostico($paciente->id_ambulatorio);
            return $cirugia;
        });

        $pdf = Pdf::loadView('reportesPDF.entregaTurno', [
            "turno" => $turno,
            "entregados" => $entregados,
            "traslados" => $traslados,
            "fallecidos" => $fallecidos,
            "cirugias" => $cirugias,
        ]);
        return $pdf->stream('entregaTurno.pdf');
    }

    public function guardarCambioTurno(CrearCambioTurnoRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Guardar entrega turno.
            $dataTurno = $request->only('fecha_llegada', 'fecha_salida', 'reemplazante', 'medico_entrega', 'medico_recibe', 'novedades', 'cirugias', 'fallecidos', 'traslados');

            // Comprobar si el turno ya existe.
            $existeTurno = RpCambioTurno::compruebaTurno($dataTurno["medico_entrega"], $dataTurno["fecha_llegada"], $dataTurno["fecha_salida"]);
            if ($existeTurno) {
                $llegada = Carbon::parse($existeTurno->fecha_llegada);
                $salida = Carbon::parse($existeTurno->fecha_salida);
                return response()->json([
                    'info' => "Ya posee un turno registrado entre {$llegada->format('d/m/Y H:i')} y {$salida->format('d/m/Y H:i')}.",
                    'id' => $existeTurno->id_cambio_turno,
                ], 409); // 409 Conflict
            }

            // Guardar nuevo turno.
            $guardarTurno = RpCambioTurno::guardarTurno($dataTurno);

            // Guardar entregados.
            $dataEntregados = $request->entregados;
            if ($dataEntregados) {
                foreach ($dataEntregados as $entregado) {
                    RpDetCambioTurno::guardarEntregados($entregado, $guardarTurno->id_cambio_turno);
                }
            }

            // Guardar traslados.
            $dataTraslados = $request->traslados;
            if ($dataTraslados) {
                foreach ($dataTraslados as $traslado) {
                    RpDetCtTraslados::guardarTraslados($traslado, $guardarTurno->id_cambio_turno);
                }
            }

            // Guardar fallecidos.
            $dataFallecidos = $request->fallecidos;
            if ($dataFallecidos) {
                foreach ($dataFallecidos as $fallecido) {
                    RpDetCtFallecidos::guardarFallecidos($fallecido, $guardarTurno->id_cambio_turno);
                }
            }

            // Guardar cirugías.
            $dataCirugias = $request->cirugias;
            if ($dataCirugias) {
                foreach ($dataCirugias as $cirugia) {
                    RpDetCtCirugias::guardarCirugias($cirugia, $guardarTurno->id_cambio_turno);
                }
            }

            DB::commit();

            return response()->json([
                'exito' => 'Turno guardado con éxito.',
                // 'turno_id' => $guardarTurno->id_cambio_turno
            ], 201); // 201 Created
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Error al guardar el turno.',
                'details' => $e->getMessage()
            ], 400); // 500 Internal Server Error
        }
    }

    public function comprobarTurnoExistente(Request $request): JsonResponse
    {
        $medico_entrega = $request->medico_entrega;
        $fecha_llegada = $request->llegada;
        $fecha_salida = $request->salida;

        $response = null;
        $existeTurno = RpCambioTurno::compruebaTurno($medico_entrega, $fecha_llegada, $fecha_salida);
        if ($existeTurno) {
            $llegada = Carbon::parse($existeTurno->fecha_llegada);
            $salida = Carbon::parse($existeTurno->fecha_salida);
            $response = [
                'msg' => "Ya posee un turno regitrado entre {$llegada->format('d/m/Y H:i')} y {$salida->format('d/m/Y H:i')}.",
                'parametro' => $existeTurno->id_cambio_turno
            ];
        }

        return response()->json(['existeTurno' => $response]);
    }

}