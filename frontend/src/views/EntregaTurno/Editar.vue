<script setup>
import { onMounted, computed, onBeforeUnmount, watchEffect } from 'vue';
import { useRoute, useRouter } from "vue-router";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import EntregaTurnoForm from '@/views/EntregaTurno/Form.vue';
import FooterInc from '@/components/FooterInc.vue';
import Button from 'primevue/button';
import { useHead } from '@vueuse/head';
import ProgressSpinner from 'primevue/progressspinner';
import { useForm } from 'laravel-precognition-vue';
import { useEntregaTurnoStore } from '@/stores/entregaTurno';
import { useEntregaTurno } from "@/composables/entregaTurno";
import { useFetch } from "@/composables/fetch";
import { alertaExito, alertaError, alertaErrores } from '@/helpers/AlertasSweetAlert';

useHead({ title: "Editar turno" });

const route = useRoute();
const id_turno = route.params.id;
const router = useRouter();
const entregaTurnoStore = useEntregaTurnoStore();

// const { medicos } = useEntregaTurno("/medicosEntregaTurno");
// const { unidades } = useEntregaTurno("/unidades");
const { data: dataMedicos, error: medicoError, fetchData: medicoFetchData } = useFetch("/medicosEntregaTurno");
const medicos = computed(() => dataMedicos.value?.data?.medicos || []);

const { data: dataUnidades, error: medicoUnidades, fetchData: unidadesFetchData } = useFetch("/unidades");
const unidades = computed(() => dataUnidades.value?.data?.unidades || []);

const loading = computed(() => entregaTurnoStore.loading_obtener);

onMounted(async() => {
    await medicoFetchData();
    await unidadesFetchData();
    await entregaTurnoStore.obtenerTurno(id_turno);
})

const form = useForm('post', '/actualizarCambioTurno', {
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

watchEffect(() => {
    if (entregaTurnoStore.turno) {
        form.entregados = entregaTurnoStore.entregados;
        form.traslados = entregaTurnoStore.traslados;
        form.fallecidos = entregaTurnoStore.fallecidos;
        form.cirugias = entregaTurnoStore.cirugias;
        form.novedades = entregaTurnoStore.turno.novedades || '';
        form.reemplazante = entregaTurnoStore.turno.reemplazante || false;
        form.medico_entrega = {
            id: entregaTurnoStore.turno.medico_entrega?.cod_prof,
            name: entregaTurnoStore.turno.medico_entrega?.sta_descripcion,
        } || '';
        form.medico_recibe = {
            id: entregaTurnoStore.turno.medico_recibe?.cod_prof,
            name: entregaTurnoStore.turno.medico_recibe?.sta_descripcion,
        } || '';
        form.fecha_llegada = entregaTurnoStore.turno.fecha_llegada || '';
        form.fecha_salida = entregaTurnoStore.turno.fecha_salida || '';
    }
})

const goBack = () => {
    window.history.back();
};

const submit = () => {
    form.submit().then(response => {
        const responseData = response.data;
        if (responseData.exito) {
            alertaExito(responseData.exito);
            form.reset();
            router.push('/misTurnos');
        } else if (responseData.error) {
            alertaError(responseData.error);
        }
    }).catch (error => {
        console.error(error);
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
        } else {
            alertaError("Se ha producido un error en la red o un error inesperado.");
        }
    });
}

</script>

<template>
    <AuthenticatedLayout>
        <Banner class="pt-4" imagenUrl="/imagenes/banner-nuevo.png" titulo="Entrega de turno">
        </Banner>
        <div class="py-4">
            <div class="w-5/6 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-4 overflow-hidden shadow-2xl sm:rounded-lg">
                    <div class="px-0 py-6 bg-white border-gray-200 mt-6">
                        <Button @click="goBack()"
                            label="Volver"
                            severity="warn">
                        </Button>
                    </div>
                    <div v-if="loading" class="flex justify-center items-center">
                        <ProgressSpinner />
                    </div>
                    <div v-else>
                        <EntregaTurnoForm
                            :updating="true"
                            :form="form"
                            :medicos="medicos"
                            :unidades="unidades"
                            @submit="submit" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <FooterInc></FooterInc>
</template>
