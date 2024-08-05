<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import EntregaTurnoForm from '@/views/EntregaTurno/Form.vue';
import FooterInc from '@/components/FooterInc.vue';
import { alertaExito, alertaError, alertaErrores, alertaPregunta } from '@/helpers/AlertasSweetAlert';

const props = defineProps({
    medico_entrega: {
        type: Object,
        default: () => ({})
    },
    medicos: {
        type: Array,
        default: () => []
    },
    unidades: {
        type: Array,
        default: () => []
    }
});

// Inicializar el formulario
const form = ref({
    entregados: [],
    traslados: [],
    fallecidos: [],
    cirugias: [],
    novedades: '',
    reemplazante: false,
    medico_entrega: props.medico_entrega ?? '',
    medico_recibe: '',
    fecha_entrada: '',
    fecha_salida: '',
});

// Router para la navegación
const router = useRouter();

// Función para manejar el submit del formulario
const submit = async () => {
    try {
        const response = await axios.post('/api/entregaTurno/guardarCambioTurno', form.value, {
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
                medico_entrega: props.medico_entrega ?? '',
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
</script>

<template>
    <AuthenticatedLayout>
        <Banner class="pt-4" imagenUrl="/imagenes/banner-nuevo.png" titulo="Entrega de turno" />
        <div class="py-4">
            <div class="w-6/6 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-4 overflow-hidden shadow-2xl sm:rounded-lg">
                    <div>
                        <div class="px-0 py-6 bg-white border-gray-200 mt-6">
                            <router-link :to="'/entregaTurno/dashboard'"
                                class="text-white bg-naranjo-light hover:bg-naranjo-dark focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">
                            Volver
                            </router-link>
                        </div>
                        <EntregaTurnoForm
                            :form="form"
                            :medicos="props.medicos"
                            :unidades="props.unidades"
                            @submit="submit" />
                    </div>
                </div>
            </div>
        </div>
        <FooterInc />
    </AuthenticatedLayout>
</template>
