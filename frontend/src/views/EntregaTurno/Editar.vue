<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import EntregaTurnoForm from '@/views/EntregaTurno/Form.vue';
import FooterInc from '@/components/FooterInc.vue';
import { alertaExito, alertaError, alertaErrores } from '@/helpers/AlertasSweetAlert';

const props = defineProps({
    turno: {
        type: Object,
        default: () => ({})
    },
    fecha_entrada: {
        type: Array,
        default: () => ({}),
    },
    fecha_salida: {
        type: Array,
        default: () => ({}),
    },
    cirugias: {
        type: Array,
        default: () => ({}),
    },
    entregados: {
        type: Array,
        default: () => ({}),
    },
    traslados: {
        type: Array,
        default: () => ({}),
    },
    fallecidos: {
        type: Array,
        default: () => ({}),
    },
    medico_entrega: {
        type: Object,
        default: () => ({})
    },
    medico_recibe: {
        type: Object,
        default: () => ({})
    },
    medicos: {
        type: Array,
        default: () => ({}),
    },
    unidades: {
        type: Array,
        default: () => ({}),
    }
});

const form = ref({
    turno: props.turno,
    entregados: props.entregados,
    traslados: props.traslados,
    fallecidos: props.fallecidos,
    cirugias: props.cirugias,
    novedades: props.turno.novedades,
    reemplazante: props.turno.reemplazante,
    medico_entrega: props.medico_entrega,
    medico_recibe: props.medico_recibe,
    fecha_entrada: props.fecha_entrada,
    fecha_salida: props.fecha_salida,
});

const router = useRouter();

const submit = async () => {
    try {
        const response = await axios.post('/entregaTurno/actualizarCambioTurno', form.value);
        let responseMessage = response.data.response_message;
        if (responseMessage.success) {
            alertaExito(responseMessage.success);
        } else if (responseMessage.error) {
            alertaError(responseMessage.error);
        }
    } catch (error) {
        alertaErrores(error.response.data.errors);
    }
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<template>
    <AuthenticatedLayout>
        <Banner class="pt-4" imagenUrl="/imagenes/banner-nuevo.png" titulo="Entrega de turno">
        </Banner>
        <div class="py-4">
            <div class="w-6/6 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-4 overflow-hidden shadow-2xl sm:rounded-lg">
                    <div>
                        <div class="px-0 py-6 bg-white border-gray-200 mt-6">
                            <router-link to="/inicio"
                                class="text-white bg-naranjo-light hover:bg-naranjo-dark focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">
                            Volver
                            </router-link>
                        </div>

                        <EntregaTurnoForm
                            :updating="true"
                            :form="form"
                            :medicos="props.medicos"
                            :unidades="props.unidades"
                            @submit="submit" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <FooterInc></FooterInc>
</template>
