<script setup>
import axios from 'axios';
import Label from '@/components/InputLabel.vue';
import Input from '@/components/TextInput.vue';
import TextArea from '@/components/TextArea.vue';
import InputError from '@/components/InputError.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import IconBuscar from '@/components/icons/IconBuscar.vue';
import IconEliminar from '@/components/icons/IconEliminar.vue';
import { validate, clean, format } from 'rut.js';
import { ref, onMounted, defineProps, defineEmits } from 'vue';
import { minDate, maxDate } from '@/helpers/AyudaFechas.js';

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
    }
});

const emit = defineEmits(['submit']);

// Función para obtener un correlativo único
const correlativoCirugias = () => (new Date()).getTime();

// Función para agregar una línea de cirugía
const agregarLineaCirugia = () => {
    props.form.cirugias.push({
        id: correlativoCirugias(),
        run: '',
        nombre: '',
        diagnostico: '',
        intervencion: '',
        fecha_inicio: '',
        fecha_fin: '',
        editable: true,
        error: {},
    });
};

// Función para quitar una línea de cirugía
const quitarLineaCirugia = (id) => {
    if (props.form.cirugias.length > 0) {
        const index = props.form.cirugias.findIndex(f => f.id === id);
        if (index !== -1) {
            props.form.cirugias.splice(index, 1);
        }
    }
};

// Función para buscar información del paciente por RUT
const buscarInfoPacienteRut = async (rut, index) => {
    if (validate(rut)) {
        rut = props.limpiarRut(rut);
        try {
            const response = await axios.get(`/obtenerInfoPaciente/${rut}`);
            let data = response.data.info_paciente;
            if (data != null) {
                props.form.cirugias[index].nombre = data.nombre_completo;
                props.form.cirugias[index].diagnostico = data.diagnostico;
            } else {
                props.form.errors[`cirugias.${index}.run`] = "El run ingresado no existe en nuestros registros.";
            }
        } catch (error) {
            console.error({ error });
        }
    } else {
        props.form.cirugias[index].error.run = "El run no es valido.";
    }
};

if (props.updating) {
    onMounted(() => {
        // Llamar a formatearRut para cada elemento de la lista
        props.form.cirugias.forEach((cirugia, index) => {
            props.formatearRut(cirugia.run, props.form.cirugias, index, 'cirugias');
            buscarInfoPacienteRut(cirugia.run, index);
        });
    });
}
</script>

<template>
    <div class="bg-cyan-200 mt-8 p-4 overflow-hidden shadow-xl sm:rounded-xl">
        <div class="flex justify-between items-center">
            <div class="flex-1 text-center">
                <Label
                    class="text-xl flex justify-center underline mb-4"
                    for="tituloCirugia"
                    value="Pacientes con cirugía" />
            </div>
            <div>
                <PrimaryButton class="bg-green-600 hover:bg-green-700" title="Agregar paciente cirugía."
                    @click.prevent="agregarLineaCirugia">
                    +
                </PrimaryButton>
            </div>
        </div>
        <div v-if="props.form.cirugias.length > 0" class="flex flex-col pt-2">
            <div class="grid grid-cols-6 gap-4 p-2" style="grid-template-columns: 2fr 2fr 2fr 2fr 2fr 1fr;">
                <div><Label class="font-black" for="run" value="Run" /></div>
                <div><Label class="font-black" for="nombre" value="Nombre" /></div>
                <div><Label class="font-black" for="diagnostico" value="Diagnóstico" /></div>
                <div><Label class="font-black" for="cirugia" value="Cirugía" /></div>
                <div><Label class="font-black" for="hora" value="Hora" /></div>
            </div>
        </div>
        <div class="flex flex-col">
            <div v-for="(cirugia, index) in props.form.cirugias" :key="cirugia.id">
                <hr class="my-2 border-t border-gray-600">
                <div class="grid grid-cols-6 gap-4 p-2" style="grid-template-columns: 2fr 2fr 2fr 2fr 2fr 1fr;">
                    <div class="flex flex-col md:flex-row">
                        <div v-if="!cirugia.editable" class="flex flex-col">
                            {{ cirugia.run }}
                        </div>
                        <div v-else class="flex flex-col md:flex-row md:items-center">
                            <div class="w-full md:w-3/4">
                                <!-- :class="{ 'border-red-400': props.form.errors[`cirugias.${index}.run`] }" -->
                                <Input v-model="cirugia.run" placeholder="Run ej: xxxxxxxx-x"
                                    :aria-label="`props.form.cirugias.${index}.run`"
                                    @input="props.formatearRut(cirugia.run, props.form.cirugias, index, 'cirugias')" type="text"
                                    class="mt-1 block w-full" autofocus />
                                <InputError class="mt-2" textColorError="text-red-500" :message="cirugia.error.run" />
                                <!-- <InputError class="mt-2" textColorError="text-red-500"                                    :message="props.form.errors[`cirugias.${index}.run`]" /> -->
                            </div>
                            <div class="mt-2 md:mt-0 md:w-1/4 md:ml-2">
                                <PrimaryButton v-if="validate(cirugia.run)" class="bg-cyan-500 hover:bg-cyan-700"
                                    @click.prevent="buscarInfoPacienteRut(cirugia.run, index)">
                                    <IconBuscar />
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <div> {{ cirugia.nombre }} </div>
                    </div>
                    <div class="flex flex-col">
                        <div> {{ cirugia.diagnostico }} </div>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex flex-col">
                            <!-- :class="{ 'border-red-400': props.form.errors[`cirugias.${index}.intervencion`] }" -->
                            <TextArea v-model="cirugia.intervencion" rows="3" cols="3" placeholder="Cirugía realizada"
                                type="text" class="mt-1 block w-full" autofocus />
                            <!-- <InputError class="mt-2" :message="props.form.errors[`cirugias.${index}.intervencion`]" /> -->
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex flex-row">
                            <label for="fecha_inicio">Inicio:</label>
                            <div class="flex flex-col">
                                <!-- :class="{ 'border-red-400': props.form.errors[`cirugias.${index}.fecha_inicio`] }" -->
                                <Input v-model="cirugia.fecha_inicio"
                                    :aria-label="`props.form.cirugias.${index}.fecha_inicio`"
                                    type="datetime-local" class="mt-1 block w-full" autofocus />
                                <!-- <InputError class="mt-2" :message="props.form.errors[`cirugias.${index}.fecha_inicio`]" /> -->
                            </div>
                        </div>
                        <div class="flex flex-row">
                            <label for="fecha_fin" class="px-2">Fin:</label>
                            <div class="flex flex-col">
                                <!-- :class="{ 'border-red-400': props.form.errors[`cirugias.${index}.fecha_fin`] }" -->
                                <Input v-model="cirugia.fecha_fin"
                                    :aria-label="`props.form.cirugias.${index}.fecha_fin`"
                                    type="datetime-local" class="mt-1 block w-full" autofocus />
                                <!-- <InputError class="mt-2" :message="props.form.errors[`cirugias.${index}.fecha_fin`]" /> -->
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <IconEliminar @click.prevent="quitarLineaCirugia(cirugia.id)" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
