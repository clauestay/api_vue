<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearCambioTurnoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // entregados.
            'entregados.*.run' => 'required',
            'entregados.*.v_run' => 'required_unless:entregados.*.run,null|exists:SINERGIA.GENINC.tab_paciente,rut_paciente',
            'entregados.*.problemas' => 'required',
            'entregados.*.examenes' => 'required',

            // fallecidos
            'fallecidos.*.run' => 'required',
            'fallecidos.*.v_run' => 'required_unless:fallecidos.*.run,null|exists:SINERGIA.GENINC.tab_paciente,rut_paciente',
            'fallecidos.*.fecha_fallecido' => 'required|date',

            // traslados
            'traslados.*.run' => 'required',
            'traslados.*.v_run' => 'required_unless:traslados.*.run,null|exists:SINERGIA.GENINC.tab_paciente,rut_paciente',
            'traslados.*.detalle.cod_unidad_origen' => 'required',
            'traslados.*.detalle.pieza_origen' => 'required',
            'traslados.*.detalle.cama_origen' => 'required',
            'traslados.*.detalle.cod_unidad_destino' => 'required',
            'traslados.*.detalle.pieza_destino' => 'required',
            'traslados.*.detalle.cama_destino' => 'required',

            // cirugias
            'cirugias.*.run' => 'required',
            'cirugias.*.v_run' => 'required_unless:cirugias.*.run,null|exists:SINERGIA.GENINC.tab_paciente,rut_paciente',
            'cirugias.*.intervencion' => 'required',
            'cirugias.*.fecha_inicio' => 'required|date|different:cirugias.*.fecha_fin|before:cirugias.*.fecha_fin',
            'cirugias.*.fecha_fin' => 'required|date|different:cirugias.*.fecha_inicio|after:cirugias.*.fecha_inicio',

            // campos seccion normal
            'novedades' => 'required',
            'medico_entrega' => 'required|different:medico_recibe',
            'medico_recibe' => 'required|different:medico_entrega',
            'fecha_llegada' => 'required|date|different:fecha_salida|before:fecha_salida',
            'fecha_salida' => 'required|date|different:fecha_llegada|after:fecha_llegada',
        ];
    }

    public function messages()
    {
        return [
            'entregados.*.v_run.exists' => 'El run ingresado no existe en nuestros registros.'
        ];
    }
}
