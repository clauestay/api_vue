<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from "vue-router";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Banner from "@/components/Banner.vue";
import Label from '@/components/InputLabel.vue';
import FooterInc from "@/components/FooterInc.vue";
import Button from 'primevue/button';
import { useHead } from "@vueuse/head";
import dayjs from 'dayjs';
import ProgressSpinner from 'primevue/progressspinner';
import { useFetch } from "@/composables/fetch";
import { manejarError, generarPdf } from '@/functions';

useHead({ title: "Detalle turno" });

const route = useRoute();
const id_turno = route.params.id;
const loading = ref(false);

const {data: dataTurno, fetchData: turnoFetchData } = useFetch(`/entrega-turno/obtenerTurno/${id_turno}`);
const turno = computed(() => dataTurno.value?.data?.turno || []);

const {data: dataEntregados, fetchData: entregadosFetchData } = useFetch(`/entrega-turno/obtenerEntregados/${id_turno}`);
const entregados = computed(() => dataEntregados.value?.data?.entregados || []);

const {data: dataTraslados, fetchData: trasladosFetchData } = useFetch(`/entrega-turno/obtenerTraslados/${id_turno}`);
const traslados = computed(() => dataTraslados.value?.data?.traslados || []);

const {data: dataFallecidos, fetchData: fallecidosFetchData } = useFetch(`/entrega-turno/obtenerFallecidos/${id_turno}`);
const fallecidos = computed(() => dataFallecidos.value?.data?.fallecidos || []);

const {data: dataCirugias, fetchData: cirugiasFetchData } = useFetch(`/entrega-turno/obtenerCirugias/${id_turno}`);
const cirugias = computed(() => dataCirugias.value?.data?.cirugias || []);

onMounted(async () => {
    loading.value = true;
    try {
        await turnoFetchData();
        await entregadosFetchData();
        await trasladosFetchData();
        await fallecidosFetchData();
        await cirugiasFetchData();
    } catch (error) {
        manejarError(error, 'Error al obtener el turno');
    } finally {
        loading.value = false;
    }
})

const sinInfo = 'Sin información';

const goBack = () => {
    window.history.back();
};

const exportarPdf = async () => {
    let url =  `/entrega-turno/generarPdfTurno/${id_turno}`;
    await generarPdf(url);
}

</script>

<template>
    <AuthenticatedLayout>
        <Banner class="pt-4" imagenUrl="/imagenes/banner-nuevo.png" titulo="Entrega de turno">
        </Banner>
        <br>
        <div class="py-2">
            <div class="w-5/6 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-2xl sm:rounded-lg">
                    <div class="p-6 bg-white border-gray-200 mt-6">
                        <Button @click="goBack()"
                            label="Volver"
                            severity="warn">
                        </Button>
                    </div>
                    <div class="text-center pb-2">
                        <Label class="text-2xl font-semibold uppercase text-gris-dark">Detalle entrega turno</Label>
                    </div>
                    <div v-if="loading" class="flex justify-center items-center">
                        <ProgressSpinner />
                    </div>
                    <div v-else>
                        <div class="p-6 bg-white">
                            <div class="flex flex-col">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="w-full p-6 bg-white border border-[#AE97C2] rounded-lg shadow-xl dark:bg-gray-800 dark:border-gray-700">
                                        <div class="flex flex-col">
                                            <div class="md:flex">
                                                <div class="md:w-1/2 md:mb-0">
                                                    <Label class="mx-10 font-bold text-gris-dark">Medico Residente:</Label>
                                                </div>
                                                <div class="md:w-1/2">
                                                    <div class="mx-10">
                                                        <Label class="text-gris-dark">
                                                            {{ turno?.medico_entrega?.sta_descripcion }}
                                                        </Label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="md:flex">
                                                <div class="md:w-1/2 md:mb-0">
                                                    <Label class="mx-10 font-bold text-gris-dark">Recibe Turno:</Label>
                                                </div>
                                                <div class="md:w-1/2">
                                                    <div class="mx-10">
                                                        <Label class="text-gris-dark">
                                                            {{ turno?.medico_recibe?.sta_descripcion }}
                                                        </Label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="md:flex">
                                                <div class="md:w-1/2 md:mb-0">
                                                    <Label class="mx-10 font-bold text-gris-dark">Fecha inicio:</Label>
                                                </div>
                                                <div class="md:w-1/2">
                                                    <div class="mx-10">
                                                        <Label class="text-gris-dark">
                                                            {{ turno?.fecha_llegada ? dayjs(turno.fecha_llegada).format('DD-MM-YYYY HH:mm') : '-' }}
                                                        </Label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="md:flex">
                                                <div class="md:w-1/2 md:mb-0">
                                                    <Label class="mx-10 font-bold text-gris-dark">Fecha fin:</Label>
                                                </div>
                                                <div class="md:w-1/2">
                                                    <div class="mx-10">
                                                        <Label class="text-gris-dark">
                                                            {{ turno?.fecha_salida ? dayjs(turno.fecha_salida).format('DD-MM-YYYY HH:mm') : '-' }}
                                                        </Label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="md:flex">
                                                <div class="md:w-1/2 md:mb-0">
                                                    <Label class="mx-10 font-bold text-gris-dark">Duración:</Label>
                                                </div>
                                                <div class="md:w-1/2">
                                                    <div class="mx-10">
                                                        <Label class="text-gris-dark">
                                                            {{ turno?.qhoras ? `${turno.qhoras} horas` : '-' }}
                                                        </Label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full flex justify-center items-center">
                                        <Button label="Generar PDF" @click="exportarPdf()" severity="help"></Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-white border-gray-200">
                            <div class="flex flex-col">
                                <div>
                                    <div class="text-center pb-2">
                                        <Label class="text-xl text-gris-dark">Pacientes entregados</Label>
                                    </div>
                                    <div class="min-w-full border-b shadow overflow-x-auto">
                                        <table class="w-full whitespace-nowrap rounded-lg overflow-hidden shadow-lg">
                                            <thead class="bg-[#DCE3FF]">
                                                <tr v-if="entregados.length > 0">
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Rut</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Nombre</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Diagnóstico</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Problemas / intervenciones</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Examenes o procedimientos pendientes</Label>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="entregado in entregados" :key="entregado.rut">
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ entregado.rut }}-{{ entregado.digito }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ entregado.nombre }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ entregado.diagnostico ?? sinInfo }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ entregado.problemas ?? sinInfo }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ entregado.examenes ?? sinInfo }}</Label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div v-if="entregados.length === 0" class="w-full">
                                            <div class="px-2 border-y">
                                                <div class="px-4 flex justify-center items-center text-center">
                                                    <Label class="font-bold text-gris-dark">No se encontraron pacientes entregados.</Label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div>
                                    <div class="text-center pb-2">
                                        <Label class="text-xl text-gris-dark">Pacientes trasladados</Label>
                                    </div>
                                    <div class="min-w-full border-b border-gray-200 shadow overflow-x-auto">
                                        <table class="w-full whitespace-nowrap rounded-lg overflow-hidden shadow-lg">
                                            <thead class="bg-[#DCE3FF]">
                                                <tr v-if="traslados.length > 0">
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Rut</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Nombre</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Diagnóstico</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Información traslado</Label>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="traslado in traslados" :key="traslado.run">
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ traslado.run }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ traslado.nombre }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ traslado.diagnostico ?? sinInfo }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="flex flex-col col-span-2">
                                                            <Label class="font-bold text-gris-dark">Desde</Label>
                                                            <div class="flex flex-row">
                                                                <div class="flex flex-col">
                                                                    <Label class="text-gris-dark">Unidad: {{ traslado.detalle.desc_unidad_origen }}</Label>
                                                                </div>
                                                                <div class="flex flex-col mx-2">
                                                                    <Label class="text-gris-dark">Pieza: {{ traslado.detalle.pieza_origen }}</Label>
                                                                </div>
                                                                <div class="flex flex-col">
                                                                    <Label class="text-gris-dark">Cama: {{ traslado.detalle.cama_origen }}</Label>
                                                                </div>
                                                            </div>
                                                            <Label class="font-bold text-gris-dark">A</Label>
                                                            <div class="flex flex-row">
                                                                <div class="flex flex-col">
                                                                    <Label class="text-gris-dark">Unidad: {{ traslado.detalle.desc_unidad_destino }}</Label>
                                                                </div>
                                                                <div class="flex flex-col mx-2">
                                                                    <Label class="text-gris-dark">Pieza: {{ traslado.detalle.pieza_destino }}</Label>
                                                                </div>
                                                                <div class="flex flex-col">
                                                                    <Label class="text-gris-dark">Cama: {{ traslado.detalle.cama_destino }}</Label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div v-if="traslados.length === 0" class="w-full">
                                            <div class="px-2 border-y">
                                                <div class="px-4 flex justify-center items-center text-center">
                                                    <Label class="font-bold text-gris-dark">No se registraron pacientes trasladados.</Label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div>
                                    <div class="text-center pb-2">
                                        <Label class="text-xl text-gris-dark">Pacientes fallecidos</Label>
                                    </div>
                                    <div class="min-w-full border-b border-gray-200 shadow overflow-x-auto">
                                        <table class="w-full whitespace-nowrap rounded-lg overflow-hidden shadow-lg">
                                            <thead class="bg-[#DCE3FF]">
                                                <tr v-if="fallecidos.length > 0">
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Rut</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Nombre</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Diagnóstico</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Fecha fallecimiento</Label>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="fallecido in fallecidos" :key="fallecido.rut">
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ fallecido.rut }}-{{ fallecido.digito }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ fallecido.nombre }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ fallecido.diagnostico ?? sinInfo }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ fallecido.fecha_fallecido }}</Label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div v-if="fallecidos.length === 0" class="w-full">
                                            <div class="px-2 border-y">
                                                <div class="px-4 flex justify-center items-center text-center">
                                                    <Label class="font-bold text-gris-dark">No se registraron Pacientes fallecidos.</Label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div>
                                    <div class="text-center pb-2">
                                        <Label class="text-xl text-gris-dark">Pacientes con cirugia</Label>
                                    </div>
                                    <div class="min-w-full border-b shadow overflow-x-auto">
                                        <table class="w-full whitespace-nowrap rounded-lg overflow-hidden shadow-lg">
                                            <thead class="bg-[#DCE3FF]">
                                                <tr v-if="cirugias.length > 0">
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Rut</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Paciente</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Diagnóstico</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Cirugia</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Hora</Label>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="cirugia in cirugias" :key="cirugia.rut">
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ cirugia.rut }}-{{ cirugia.digito }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ cirugia.nombre }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ cirugia.diagnostico }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ cirugia.intervencion }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">
                                                                Inicio: {{ cirugia.fecha_inicio }}
                                                            </Label>
                                                            <br>
                                                            <Label class="text-gris-dark">Fin: {{ cirugia.fecha_fin }}</Label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div v-if="cirugias.length === 0" class="w-full">
                                            <div class="px-2 border-y">
                                                <div class="px-4 flex justify-center items-center text-center">
                                                    <Label class="font-bold text-gris-dark">No se registraron Pacientes con cirugias.</Label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-8 p-4 overflow-hidden shadow-2xl sm:rounded-lg border border-[#AE97C2]">
                                    <div class="flex flex-col">
                                        <div class="mx-10 md:flex mb-6">
                                            <div class="md:w-full px-3">
                                                <div>
                                                    <Label class="font-bold text-gris-dark">Novedades del turno</Label>
                                                    <Label class="text-gris-dark">{{ turno?.novedades }}</Label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mx-10 md:flex mb-6">
                                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                                <Label class="font-bold text-gris-dark">Cantidad entregados</Label>
                                                <Label class="text-gris-dark">{{ entregados.length }}</Label>
                                            </div>
                                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                                <Label class="font-bold text-gris-dark">Cantidad cirugías realizadas por residencia</Label>
                                                <Label class="text-gris-dark">{{ cirugias.length }}</Label>
                                            </div>
                                            <div class="md:w-1/2 px-3">
                                                <div>
                                                    <Label class="font-bold text-gris-dark">Cantidad pacientes fallecidos</Label>
                                                    <Label class="text-gris-dark">{{ fallecidos.length }}</Label>
                                                </div>
                                            </div>
                                            <div class="md:w-1/2 px-3">
                                                <div>
                                                    <Label class="font-bold text-gris-dark">Cantidad pacientes trasladados</Label>
                                                    <Label class="text-gris-dark">{{ traslados.length }}</Label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <FooterInc></FooterInc>
</template>
