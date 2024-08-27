<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="{{ public_path('css/app.css') }}"> -->

    <title>Entrega de Turno</title>
</head>

<style>
    body {
        font-size: 10px;
        padding: 0px;
    }

    .cabezera {
        width: 100%;
    }
</style>

<body>
    <legend class="text-center">Entrega de Turno</legend>
    <p class="text-center"><b>Fecha Inicio:</b> {{ \Carbon\Carbon::parse($turno->fecha_llegada)->format('d/m/Y H:i') }} - <b>Fecha Fin:</b> {{ \Carbon\Carbon::parse($turno->fecha_salida)->format('d/m/Y H:i') }}<br>
    <b>Duración:</b> {{ $turno->qhoras ? $turno->qhoras . ' horas' : 'Sin información' }}</p>

    <legend class="text-center">Entregados</legend>
    <table class="table">
        <thead>
            @if ($entregados->count() > 0)
            <tr>
                <th scope="col">Rut</th>
                <th scope="col">Nombre</th>
                <th scope="col">Diagnóstico</th>
                <th scope="col">Problemas / Intervenciones</th>
                <th scope="col">Examenes o Procedimientos Pendientes</th>
            </tr>
            @endif
        </thead>
        <tbody>
            @forelse ($entregados as $entregado)
            <tr>
                <td>
                    {{ $entregado->rut }}{{ $entregado->rut ? '-' : '-' }}{{$entregado->digito}}
                </td>
                <td>
                    {{ $entregado->nombre_completo }}
                </td>
                <td>
                    {{ $entregado->diagnostico ? $entregado->diagnostico : 'Sin información' }}
                </td>
                <td>
                    {{ $entregado->problemas ? $entregado->problemas : 'Sin información' }}
                </td>
                <td>
                    {{ $entregado->examenes ? $entregado->examenes : 'Sin información' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <p class="text-center">Sin entregas</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <legend class="text-center">Traslados</legend>
    <table class="table">
        <thead>
            @if ($traslados->count() > 0)
            <tr>
                <th scope="col">Rut</th>
                <th scope="col">Nombre</th>
                <th scope="col">Diagnóstico</th>
                <th scope="col">Información traslado</th>
            </tr>
            @endif
        </thead>
        <tbody>
            @forelse ($traslados as $traslado)
            <tr>
                <td scope="fila">
                    {{ $traslado["run"] }}
                </td>
                <td>
                    {{ $traslado["nombre_completo"] }}
                </td>
                <td>
                    {{ $traslado["diagnostico"] ? $traslado["diagnostico"] : 'Sin información' }}
                </td>
                <td>
                    <div class="fila">
                        Desde: Unidad: {{ $traslado["detalle"]["cod_unidad_origen"] }} Pieza: {{ $traslado["detalle"]["pieza_origen"] }} Cama: {{ $traslado["detalle"]["cama_origen"] }}
                    </div>
                    <div class="fila">
                        A: Unidad: {{ $traslado["detalle"]["cod_unidad_destino"] }} Pieza: {{ $traslado["detalle"]["pieza_destino"] }} Cama: {{ $traslado["detalle"]["cama_destino"] }}
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">
                    <p class="text-center">Sin traslados</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <legend class="text-center">Fallecidos</legend>
    <table class="table">
        <thead>
            @if ($fallecidos->count() > 0)
            <tr>
                <th scope="col">Rut</th>
                <th scope="col">Nombre</th>
                <th scope="col">Diagnóstico</th>
                <th scope="col">Fecha Fallecimiento</th>
            </tr>
            @endif
        </thead>
        <tbody>
            @forelse ($fallecidos as $fallecido)
            <tr>
                <td>
                    {{ $fallecido["run"] }}
                </td>
                <td>
                    {{ $fallecido["nombre_completo"] }}
                </td>
                <td>
                    {{ $fallecido["diagnostico"] ? $fallecido["diagnostico"] : 'Sin información' }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($fallecido["fecha_fallecido"])->format('d/m/Y H:i') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">
                    <p class="text-center">Sin fallecidos</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <legend class="text-center">Cirugias</legend>
    <table class="table">
        <thead>
            @if ($cirugias->count() > 0)
            <tr>
                <th scope="col">Rut</th>
                <th scope="col">Nombre</th>
                <th scope="col">Diagnóstico</th>
                <th scope="col">Cirugía</th>
                <th scope="col">Hora</th>
            </tr>
            @endif
        </thead>
        <tbody>
            @forelse ($cirugias as $cirugia)
            <tr>
                <td>
                    {{ $cirugia["rut"] }} - {{$cirugia["digito"]}}
                </td>
                <td>
                    {{ $cirugia["nombre_completo"] }}
                </td>
                <td>
                    {{$cirugia["diagnostico"] ? $cirugia["diagnostico"] : 'Sin información'}}
                </td>
                <td>
                    {{ $cirugia["intervencion"] ? $cirugia["intervencion"] : 'Sin información' }}
                </td>
                <td>
                    <p>Inicio: {{ \Carbon\Carbon::parse($cirugia["hora_inicio_cirugia"])->format('d/m/Y H:i') }}</p>
                    <p>Fin: {{ \Carbon\Carbon::parse($cirugia["hora_termino_cirugia"])->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">
                    <p class="text-center">Sin cirugías</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <legend class="text-center">Datos</legend>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Novedades del turno:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $turno->novedades ? $turno->novedades : 'Sin novedades' }}
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Cantidad Entregas:</th>
                <th scope="col">Cantidad Cirugías realizadas por residencia:</th>
                <th scope="col">Cantidad Pacientes fallecidos:</th>
                <th scope="col">Cantidad Traslados:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ count($entregados) }}</td>
                <td>{{ count($cirugias) }}</td>
                <td>{{ count($fallecidos) }}</td>
                <td>{{ count($traslados) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <table class="cabezera">
            <thead>
                <tr>
                    <th scope="col" class="text-center">
                        ________________________________
                        <p>Profesional Medico residente</p>
                        <p>{{ $turno->medicoEntrega->sta_descripcion }}</p>
                    </th>
                    <th scope="col" class="text-center">
                        ________________________________
                        <p>Recibe Turno conforme</p>
                        <p>{{ $turno->medicoRecibe->sta_descripcion }}</p>
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</body>

</html>