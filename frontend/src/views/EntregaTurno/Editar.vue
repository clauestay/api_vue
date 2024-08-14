<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import EntregaTurnoForm from '@/views/EntregaTurno/Form.vue';
import FooterInc from '@/components/FooterInc.vue';
import { alertaExito, alertaError, alertaErrores } from '@/helpers/AlertasSweetAlert';
import { Head, useHead } from '@vueuse/head';
import { useAuthStore } from "@/stores/auth";
import dayjs from 'dayjs';
import ProgressSpinner from 'primevue/progressspinner';

const authStore = useAuthStore();
axios.defaults.headers.common["Authorization"] = "Bearer " + authStore.authToken;

useHead({ title: "Detalle turno" });

const router = useRoute();
const id_turno = router.params.id;

const goBack = () => {
    window.history.back();
};

const errors = ref(null);
const loading = ref(false);
const turno = ref([]);
const entregados = ref([]);
const traslados = ref([]);
const fallecidos = ref([]);
const cirugias = ref([]);
const medico_entrega = ref({});
const medico_recibe = ref({});
const medicos = ref([]);
const unidades = ref({});

const obtenerTurno = async (id_turno) => {
    loading.value = true;
    try {
        const response = await axios.get(`/obtenerTurno/${id_turno}`);
        turno.value = response.data.turno;
        await obtenerEntregados(id_turno);
        await obtenerTraslados(id_turno);
        await obtenerFallecidos(id_turno);
        await obtenerCirugias(id_turno);
    } catch (err) {
    console.log(err);
    if (error.response && err.response.data) {
      error.value = err.response.data.error;
      alertaError(error.value);
    } else {
      error.value = "Error al obtener el listado de los turnos.";
      alertaError(error.value);
    }
  }
    loading.value = false;
}

const obtenerEntregados = async (id_turno) => {
    try {
        const response = await axios.get(`/obtenerEntregados/${id_turno}`);
        entregados.value = response.data.entregados;
    } catch (err) {
    console.log(err);
    if (err.response && err.response.entregados) {
      error.value = err.response.data.error;
      alertaError(error.value);
    } else {
      error.value = "Error al obtener el listado de los pacientes entregados.";
      alertaError(error.value);
    }
  }
}

const obtenerTraslados = async (id_turno) => {
    try {
        const response = await axios.get(`/obtenerTraslados/${id_turno}`);
        traslados.value = response.data.traslados;
    } catch (err) {
    console.log(err);
    if (error.response && err.response.traslados) {
      error.value = err.response.data.error;
      alertaError(error.value);
    } else {
      error.value = "Error al obtener el listado de los pacientes trasladados.";
      alertaError(error.value);
    }
  }
}

const obtenerFallecidos = async (id_turno) => {
    try {
        const response = await axios.get(`/obtenerFallecidos/${id_turno}`);
        fallecidos.value = response.data.fallecidos;
    } catch (err) {
    console.log(err);
    if (error.response && err.response.fallecidos) {
      error.value = err.response.data.error;
      alertaError(error.value);
    } else {
      error.value = "Error al obtener el listado de los pacientes fallecidos.";
      alertaError(error.value);
    }
  }
}

const obtenerCirugias = async (id_turno) => {
    try {
        const response = await axios.get(`/obtenerCirugias/${id_turno}`);
        cirugias.value = response.data.cirugias;
    } catch (err) {
    console.log(err);
    if (error.response && err.response.cirugias) {
      error.value = err.response.data.error;
      alertaError(error.value);
    } else {
      error.value = "Error al obtener el listado de los pacientes cirugias.";
      alertaError(error.value);
    }
  }
}

const form = ref({
    turno: turno,
    entregados: entregados,
    traslados: traslados,
    fallecidos: fallecidos,
    cirugias: cirugias,
    novedades: turno.novedades,
    reemplazante: turno.reemplazante,
    medico_entrega: medico_entrega,
    medico_recibe: medico_recibe,
});

const submit = async () => {
    try {
        const response = await axios.post('/actualizarCambioTurno', form.value, {
            headers: {
                'Content-Type': 'application/json',
            }
        });

        const responseData = response.data;

        if (responseMessage.exito) {
            alertaExito(responseMessage.success);
        } else if (responseMessage.error) {
            alertaError(responseMessage.error);
        }
    } catch (error) {
        alertaErrores(error.response.data.errors);
    }
};
</script>

<!-- <style src="vue-multiselect/dist/vue-multiselect.css"></style> -->

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
