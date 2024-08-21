<script setup>
import { computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import EntregaTurnoForm from '@/views/EntregaTurno/Form.vue';
import FooterInc from '@/components/FooterInc.vue';
import { Head, useHead } from '@vueuse/head';
import { useAuthStore } from "@/stores/auth";
import { useEntregaTurnoStore } from '@/stores/entregaTurno';
import { useForm } from 'laravel-precognition-vue';
import { alertaExito, alertaError, alertaErrores } from '@/helpers/AlertasSweetAlert';

const authStore = useAuthStore();
const cod_prof = authStore.authUser.codigo_profesional?.cod_prof;

const entregaTurnoStore = useEntregaTurnoStore();

onMounted(() => {
    entregaTurnoStore.resetForm();
    getMedicoEntrega(cod_prof);
    // entregaTurnoStore.getMedicoEntrega(cod_prof);
    entregaTurnoStore.getMedicos();
    entregaTurnoStore.getUnidades();
})

useHead({
    title: 'Crear turno',
});

const router = useRouter();

// const errors = computed(() => entregaTurnoStore.errors);
// const form = computed(() => entregaTurnoStore.form);
const medicos = computed(() => entregaTurnoStore.medicos);
const unidades = computed(() => entregaTurnoStore.unidades);

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

const getMedicoEntrega = async (cod_prof) => {
    try {
        const response = await axios.get(`/medicoEntregaTurno/${cod_prof}`);
        form.medico_entrega = response.data.medico_entrega;
    } catch (err) {
        console.log(err);
        entregaTurnoStore.manejarError(err, "Error al obtener el listado de los turnos.");
    }
}

const submit = () => {
    form.submit().then(response => {
        console.log({response});
        const responseData = response.data;

        if (responseData.exito) {
            alertaExito(responseData.exito);
            this.resetForm();
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
