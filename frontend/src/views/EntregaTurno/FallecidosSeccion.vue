<script setup>
import axios from 'axios';
import Label from '@/components/InputLabel.vue';
import Input from '@/components/TextInput.vue';
import InputError from '@/components/InputError.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import IconBuscar from '@/components/icons/IconBuscar.vue';
import IconEliminar from '@/components/icons/IconEliminar.vue';
import NProgress from 'nprogress';
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
    updating: {
        type: Boolean,
        required: false,
        default: false
    }
});

const emit = defineEmits(['submit']);

// Función para obtener un correlativo único
const correlativoFallecidos = () => (new Date()).getTime();

// Función para agregar una línea de fallecido
const agregarLineaFallecido = () => {
    props.form.fallecidos.push({
        id: correlativoFallecidos(),
        run: '',
        nombre: '',
        diagnostico: '',
        fecha_fallecido: '',
        editable: true,
        error: {},
    });
};

// Función para quitar una línea de fallecido
const quitarLineaFallecido = (id) => {
    if (props.form.fallecidos.length > 0) {
        const index = props.form.fallecidos.findIndex(f => f.id === id);
        if (index !== -1) {
            props.form.fallecidos.splice(index, 1);
        }
    }
};

// Función para buscar información del paciente fallecido por RUT
const buscarInfoPacienteFallecido = async (rut, index) => {
    NProgress.start();
    if (validate(rut)) {
        rut = props.limpiarRut(rut);
        try {
            const response = await axios.get(`/buscarInfoPacienteRut/${rut}`);
            let data = response.data.info_paciente;
            props.form.fallecidos[index].nombre = data.nombre_completo;
            props.form.fallecidos[index].diagnostico = data.diagnostico;
            // props.form.fallecidos[index].fecha_fallecido = data.fecha_fallecido;
        } catch (error) {
            console.error({ error });
            props.form.fallecidos[index].error.run = "El run no existe.";
        }
    } else {
        props.form.fallecidos[index].error.run = "El run no es valido.";
    }
    NProgress.done();
};

if (props.updating) {
    onMounted(() => {
        props.form.fallecidos.forEach((fallecido, index) => {
            props.formatearRut(fallecido.run, props.form.fallecidos, index, 'fallecidos');
            buscarInfoPacienteFallecido(fallecido.run, index);
        });
    });
}
</script>

<template>
    <div class="bg-slate-600 mt-8 p-4 overflow-hidden shadow-xl sm:rounded-xl">
        <Label class="text-xl flex justify-center underline mb-4 text-white" for="tituloFallecido"
            value="Pacientes fallecidos" />
        <div class="flex justify-end">
            <div class="float-right">
                <PrimaryButton class="bg-green-600 hover:bg-green-700" title="agregar paciente fallecido."
                    @click.prevent="agregarLineaFallecido">
                    +
                </PrimaryButton>
            </div>
        </div>
        <div class="flex flex-col pt-2">
            <div class="grid grid-cols-5 gap-4 p-2">
                <div><Label class="font-black text-white" for="run" value="Run" /></div>
                <div><Label class="font-black text-white" for="nombre" value="Nombre" /></div>
                <div><Label class="font-black text-white" for="diagnostico" value="Diagnóstico" /></div>
                <div><Label class="font-black text-white" for="fecha_fallecido" value="Fecha fallecido" /></div>
            </div>
        </div>
        <div class="flex flex-col">
            <div v-for="(fallecido, index) in props.form.fallecidos" :key="fallecido.id">
                <hr class="my-2 border-t border-white">
                <div class="grid grid-cols-5 gap-4 p-2">
                    <div class="flex flex-col md:flex-row">
                        <div v-if="fallecido.editable == false" class="flex flex-col text-white">
                            {{ fallecido.run }}
                        </div>
                        <div v-else class="flex flex-col md:flex-row md:items-center">
                            <div class="w-full md:w-3/4">
                                <!-- :class="{ 'border-red-400': props.form.errors[`fallecidos.${index}.run`] }" -->
                                <Input v-model="fallecido.run" placeholder="Run ej: xxxxxxxx-x"
                                    :aria-label="`props.form.fallecidos.${index}.run`"
                                    @input="props.formatearRut(fallecido.run, props.form.fallecidos, index, 'fallecidos')" type="text"
                                    class="mt-1 block w-full" autofocus />
                                <InputError class="mt-2" textColorError="text-red-500" :message="fallecido.error.run" />
                                <!-- <InputError class="mt-2" textColorError="text-red-500" :message="props.form.errors[`fallecidos.${index}.run`]" /> -->
                            </div>
                            <div class="mt-2 md:mt-0 md:w-1/4 md:ml-2">
                                <PrimaryButton v-if="validate(fallecido.run)" class="bg-cyan-500 hover:bg-cyan-700"
                                    @click.prevent="buscarInfoPacienteFallecido(fallecido.run, index)">
                                    <IconBuscar />
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <div class="text-white"> {{ fallecido.nombre }} </div>
                    </div>
                    <div class="flex flex-col">
                        <div class="text-white"> {{ fallecido.diagnostico }} </div>
                    </div>
                    <div class="flex flex-col">
                        <!-- :class="{ 'border-red-400': props.form.errors[`fallecidos.${index}.fecha_fallecido`] }" -->
                        <Input v-model="fallecido.fecha_fallecido"
                            :aria-label="`props.form.fallecidos.${index}.fecha_fallecido`" type="datetime-local"
                            class="mt-1 block w-full" autofocus />
                        <!-- <InputError class="mt-2" textColorError="text-red-500" :message="props.form.errors[`fallecidos.${index}.fecha_fallecido`]" /> -->
                    </div>
                    <div class="flex">
                        <IconEliminar @click.prevent="quitarLineaFallecido(fallecido.id)" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
