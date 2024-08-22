<script setup>
import { computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import EntregaTurnoForm from '@/views/EntregaTurno/Form.vue';
import FooterInc from '@/components/FooterInc.vue';
import { Head, useHead } from '@vueuse/head';
import { useAuthStore } from "@/stores/auth";
import { useEntregaTurno } from "@/composables/entregaTurno";
import { useFetch } from "@/composables/fetch";
import { manejarError } from '@/functions';
import { useForm } from 'laravel-precognition-vue';
import { alertaExito, alertaError, alertaErrores } from '@/helpers/AlertasSweetAlert';

const authStore = useAuthStore();
const cod_prof = authStore.authUser.codigo_profesional?.cod_prof;

const { data: dataMedicos, error: medicoError, fetchData: medicoFetchData } = useFetch("/medicosEntregaTurno");
const medicos = computed(() => dataMedicos.value?.data?.medicos || []);

const { data: dataUnidades, error: medicoUnidades, fetchData: unidadesFetchData } = useFetch("/unidades");
const unidades = computed(() => dataUnidades.value?.data?.unidades || []);

const { data: dataMedicoEntrega, error: medicoMedicoEntrega, fetchData: medicoEntregaFetchData } = useFetch(`/medicoEntregaTurno/${cod_prof}`);
const medico_entrega = computed(() => dataMedicoEntrega.value?.data?.medico_entrega || []);

onMounted(async () => {
    await medicoFetchData();
    await unidadesFetchData();
    await medicoEntregaFetchData();
});

useHead({
    title: 'Crear turno',
});

const router = useRouter();

const form = useForm('post', '/guardarCambioTurno', {
    id_turno: '',
    entregados: [],
    traslados: [],
    fallecidos: [],
    cirugias: [],
    novedades: '',
    reemplazante: false,
    medico_entrega: '',
    medico_recibe: '',
    fecha_llegada: '',
    fecha_salida: '',
});

watch(medico_entrega, (newVal) => {
    form.medico_entrega = newVal;
});

const submit = () => {
    form.submit().then(response => {
        console.log({response});
        const responseData = response.data;

        if (responseData.exito) {
            alertaExito(responseData.exito);
            form.reset();
            router.push('/misTurnos');
        } else if (responseData.error) {
            alertaError(responseData.error);
        }
    }).catch(error => {
        console.error({error});
        const responseData = error.response?.data;

        if (responseData) {
            if (error.response.status === 409) {
                alertaError(responseData.info);
            } else if (responseData.errors) {
                alertaErrores(responseData.errors);
            } else if (responseData.error) {
                alertaError(responseData.error);
            } else {
                alertaError("Se ha producido un error desconocido.");
            }
        }
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head/>
        <Banner class="pt-4" imagenUrl="/imagenes/banner-nuevo.png" titulo="Entrega de turno" />
        <br>
        <div class="py-4">
            <div class="w-5/6 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-4 overflow-hidden shadow-2xl sm:rounded-lg">
                    <div>
                        <div class="px-0 py-6 bg-white border-gray-200 mt-6">
                            <router-link :to="'/inicio'"
                                class="text-white bg-naranjo-light hover:bg-naranjo-dark focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">
                            Volver
                            </router-link>
                        </div>
                        <EntregaTurnoForm
                            :form="form"
                            :medicos="medicos"
                            :unidades="unidades"
                            @submit="submit" />
                    </div>
                </div>
            </div>
        </div>
        <FooterInc />
    </AuthenticatedLayout>
</template>
