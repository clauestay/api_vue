<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import EntregaTurnoForm from '@/views/EntregaTurno/Form.vue';
import FooterInc from '@/components/FooterInc.vue';
import { alertaExito, alertaError, alertaErrores, alertaPregunta } from '@/helpers/AlertasSweetAlert';
import { Head, useHead } from '@vueuse/head';
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
console.log(authStore.authUser);
axios.defaults.headers.common["Authorization"] =
  "Bearer " + authStore.authToken;

useHead({
    title: 'Crear turno',
});

const cod_prof = authStore.authUser.codigo_profesional?.cod_prof;

let medicos = ref([]);
let medico_entrega = ref({});
let unidades = ref({});

// Inicializar el formulario
const form = ref({
    entregados: [],
    traslados: [],
    fallecidos: [],
    cirugias: [],
    novedades: '',
    reemplazante: false,
    medico_entrega: '',
    medico_recibe: '',
    fecha_entrada: '',
    fecha_salida: '',
});

// Router para la navegación
const router = useRouter();

const getMedicoEntrega = async () => {
    try {
        const response = await axios.get(`/medicoEntregaTurno/${cod_prof}`);
        // console.log(response);
        form.value.medico_entrega = response.data.medico_entrega;
        console.log(form.value.medico_entrega);
    } catch (error) {
        console.error(error);
        alertaErrores(error);
    }
};

const getMedicos = async () => {
    try {
        const response = await axios.get('/medicosEntregaTurno');
        medicos.value = response.data.medicos;
    } catch (error) {
        console.error(error);
        alertaErrores(error);
    }
};

// Función para manejar el submit del formulario
const submit = async () => {
    try {
        const response = await axios.post('/guardarCambioTurno', form.value, {
            headers: {
                'Content-Type': 'application/json'
            }
        });
        console.log(response);

        const responseData = response.data;
        if (responseData.success) {
            alertaExito(responseData.success);
            form.value = {
                entregados: [],
                traslados: [],
                fallecidos: [],
                cirugias: [],
                novedades: '',
                reemplazante: false,
                medico_entrega: medico_entrega ?? '',
                medico_recibe: '',
                fecha_entrada: '',
                fecha_salida: '',
            };
            router.push('/entregaTurno/misTurnos');
        } else if (responseData.error) {
            alertaError(responseData.error);
        } else if (responseData.data) {
            alertaPregunta('/entregaTurno/editarTurno', responseData.data, responseData.parametro);
        }
    } catch (error) {
        console.error(error);
        alertaErrores(error);
    }
};

onMounted(() => {
    getMedicoEntrega();
    getMedicos();
})
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
