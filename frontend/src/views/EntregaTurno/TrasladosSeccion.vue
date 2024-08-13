<script setup>
import axios from 'axios';
import Label from '@/components/InputLabel.vue';
import Input from '@/components/TextInput.vue';
import InputError from '@/components/InputError.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import IconBuscar from '@/components/icons/IconBuscar.vue';
import IconEliminar from '@/components/icons/IconEliminar.vue';
import { validate, clean, format } from 'rut.js';
import { ref, onMounted, defineProps, defineEmits } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
    formatearRut: {
        type: Function,
        required: true
    },
    limpiarRut: {
        type: Function,
        required: true
    },
    unidades: {
        type: Array,
        required: true
    },
    updating: {
        type: Boolean,
        required: false,
    }
});

const emit = defineEmits(['submit']);

// Función para obtener un correlativo único
const correlativoTraslados = () => (new Date()).getTime();

// Función para agregar una línea de traslado
const agregarLineaTraslado = () => {
    props.form.traslados.push({
        id: correlativoTraslados(),
        run: '',
        nombre: '',
        diagnostico: '',
        detalle: {
            cod_unidad_origen: '',
            pieza_origen: '',
            cama_origen: '',
            cod_unidad_destino: '',
            pieza_destino: '',
            cama_destino: ''
        },
        editable: true,
        error: {},
    });
};

// Función para quitar una línea de traslado
const quitarLineaTraslado = (id) => {
    if (props.form.traslados.length > 0) {
        const index = props.form.traslados.findIndex(f => f.id === id);
        if (index !== -1) {
            props.form.traslados.splice(index, 1);
        }
    }
};

// Función para buscar información del paciente trasladado por RUT
const buscarTrasladoPacienteRut = async (rut, index) => {
    if (validate(rut)) {
        rut = props.limpiarRut(rut);
        try {
            const response = await axios.get(`/buscarTrasladoPacienteRut/${rut}`);
            console.log({response});
            let data = response.data;
            props.form.traslados[index].nombre = data.info_traslados.nombre_completo;
            props.form.traslados[index].diagnostico = data.info_traslados.diagnostico;
            if (data.info_traslados.detalle.length > 0) {
                props.form.traslados[index].detalle = data.info_traslados.detalle;
            }
        } catch (error) {
            console.error({ error });
        }
    } else {
        props.form.traslados[index].error.run = "El run no es valido.";
    }
};

if (props.updating) {
    onMounted(() => {
        // Llamar a formatearRut para cada elemento de la lista
        props.form.traslados.forEach((traslado, index) => {
            props.formatearRut(traslado.run, props.form.traslados, index, 'traslados');
            buscarTrasladoPacienteRut(traslado.run, index);
        });
    });
}
</script>

<template>
    <div class="bg-green-200 mt-8 p-4 overflow-hidden shadow-xl sm:rounded-lg">
        <div class="flex justify-center items-center">
            <div class="flex-1 text-center">
                <Label class="text-xl flex justify-center underline mb-4" for="tituloTraslados" value="Pacientes trasladados" />
            </div>
            <div>
                <PrimaryButton class="bg-green-600 hover:bg-green-700" title="agregar paciente trasladado en turno."
                    @click.prevent="agregarLineaTraslado">
                    +
                </PrimaryButton>
            </div>
        </div>
        <div v-if="props.form.traslados.length > 0" class="flex flex-col pt-2">
            <div class="grid grid-cols-6 gap-4 p-2" style="grid-template-columns: 2fr 2fr 2fr 2fr 2fr 1fr;">
                <div><Label class="font-black" for="run" value="Run" /></div>
                <div><Label class="font-black" for="nombre" value="Nombre" /></div>
                <div><Label class="font-black" for="diagnostico" value="Diagnóstico" /></div>
                <div><Label class="font-black" for="info_traslado" value="Información traslado" /></div>
                <div></div>
            </div>
        </div>

        <div class="flex flex-col">
            <div v-for="(traslado, index) in props.form.traslados" :key="traslado.id">
                <hr class="my-2 border-t border-gray-600">
                <div class="grid grid-cols-6 gap-4 p-2" style="grid-template-columns: 2fr 2fr 2fr 2fr 2fr 1fr;">
                    <div class="flex flex-col md:flex-row">
                        <div v-if="!traslado.editable" class="flex flex-col">
                            {{ traslado.run }}
                        </div>
                        <div v-else class="flex flex-col md:flex-row md:items-center">
                            <!-- :class="{ 'border-red-400': props.form.errors[`traslados.${index}.run`] }" -->
                            <div class="w-full md:w-3/4">
                                <Input v-model="traslado.run" placeholder="Run ej: xxxxxxxx-x"
                                    :aria-label="`props.form.traslado.${index}.run`"
                                    @input="props.formatearRut(traslado.run, props.form.traslados, index, 'traslados')" type="text"
                                    class="mt-1 block w-full" autofocus />
                                <InputError class="mt-2" :message="traslado.error.run" />
                                <!-- <InputError class="mt-2" :message="props.form.errors[`traslados.${index}.run`]" /> -->
                            </div>
                            <div class="mt-2 md:mt-0 md:w-1/4 md:ml-2">
                                <PrimaryButton v-if="validate(traslado.run)" class="bg-cyan-500 hover:bg-cyan-700"
                                    @click.prevent="buscarTrasladoPacienteRut(traslado.run, index)">
                                    <IconBuscar />
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span> {{ traslado.nombre }} </span>
                    </div>
                    <div class="flex flex-col">
                        <span> {{ traslado.diagnostico }}</span>
                    </div>
                    <div class="flex flex-col col-span-2">
                        <label>Desde:</label>
                        <div class="flex flex-row">
                            <div class="flex flex-col">
                                <select
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    v-model="traslado.detalle.cod_unidad_origen">
                                    <option disabled selected>Seleccionar</option>
                                    <option v-for="u in props.unidades" :key="u.cod_unidad" :value="u.cod_unidad">
                                        {{ u.desc_unidad }}</option>
                                </select>
                                <!-- <InputError class="mt-2" :message="props.form.errors[`traslados.${index}.detalle.cod_unidad_origen`]" /> -->
                            </div>
                            <div class="flex flex-col">
                                <Input class="text-sm" v-model="traslado.detalle.pieza_origen" type="text"
                                    placeholder="Pieza" />
                                <!-- <InputError class="mt-2" :message="props.form.errors[`traslados.${index}.detalle.pieza_origen`]" /> -->
                            </div>
                            <div class="flex flex-col">
                                <Input class="text-sm" v-model="traslado.detalle.cama_origen" type="text"
                                    placeholder="Cama" />
                                <!-- <InputError class="mt-2" :message="props.form.errors[`traslados.${index}.detalle.cama_origen`]" /> -->
                            </div>
                        </div>
                        <label>A:</label>
                        <div class="flex flex-row">
                            <div class="flex flex-col">
                                <select
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    v-model="traslado.detalle.cod_unidad_destino">
                                    <option disabled selected>Seleccionar</option>
                                    <option v-for="u in props.unidades" :key="u.cod_unidad" :value="u.cod_unidad">
                                        {{ u.desc_unidad }}</option>
                                </select>
                                <!-- <InputError class="mt-2" :message="props.form.errors[`traslados.${index}.detalle.cod_unidad_destino`]" /> -->
                            </div>
                            <div class="flex flex-col">
                                <Input class="text-sm" v-model="traslado.detalle.pieza_destino" type="text"
                                    placeholder="Pieza" />
                                <!-- <InputError class="mt-2"                                 :message="props.form.errors[`traslados.${index}.detalle.pieza_destino`]" /> -->
                            </div>
                            <div class="flex flex-col">
                                <Input class="text-sm" v-model="traslado.detalle.cama_destino" type="text"
                                    placeholder="Cama" />
                                <!-- <InputError class="mt-2"                                    :message="props.form.errors[`traslados.${index}.detalle.cama_destino`]" /> -->
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <IconEliminar @click.prevent="quitarLineaTraslado(traslado.id)" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
