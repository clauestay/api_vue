<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import EntregaTurnoForm from '@/views/EntregaTurno/Form.vue';
import FooterInc from '@/components/FooterInc.vue';
import { alertaExito, alertaError, alertaErrores } from '@/helpers/AlertasSweetAlert';

const turno = ref({});
const cirugias = ref({});
const entregados = ref({});
const traslados = ref({});
const fallecidos = ref({});
const medico_entrega = ref({});
const medico_recibe = ref({});
const medicos = ref({});
const unidades = ref({});

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
    // fecha_entrada: fecha_entrada,
    // fecha_salida: fecha_salida,
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
