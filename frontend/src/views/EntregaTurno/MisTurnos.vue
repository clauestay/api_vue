<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import debounce from 'lodash/debounce';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import FooterInc from '@/components/FooterInc.vue';
// import Pagination from "@/components/Pagination.vue";
import IconIr from '@/components/icons/IconIr.vue';
import IconEdit from '@/components/icons/IconEdit.vue';
import IconEliminar from '@/components/icons/IconEliminar.vue';
import Label from '@/components/InputLabel.vue';
import { alertaExito, alertaError, alertaErrores } from '@/helpers/AlertasSweetAlert';
import Swal from 'sweetalert2';

const props = defineProps({
    titulo: {
        type: Object,
        default: () => ({}),
    },
    turnos: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    }
});

const search = ref(props.filters.search);
const router = useRouter();

watch(search, debounce(() => {
    router.push({ name: 'entregaTurno.misTurnos', query: { search: search.value } });
}, 300));

const detalle_turno = (id_turno) => {
    router.push({ name: 'entregaTurno.detalleTurno', params: { id: id_turno } });
}

const editar_turno = (id_turno) => {
    router.push({ name: 'entregaTurno.editarTurno', params: { id: id_turno } });
}

const borrar_turno = (id_turno) => {
    Swal.fire({
        title: '¿Desea eliminar el turno?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        confirmButtonColor: 'green',
        denyButtonText: `Cancelar`,
        cancelButtonColor: 'red'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.delete(route('entregaTurno.borrarTurno', id_turno), {
                    preserveScroll: true,
                    preserveState: true,
                });
                let data = response.data;
                if (data.success) {
                    alertaExito(data.success);
                } else if (data.error) {
                    alertaError(data.error);
                }
            } catch (error) {
                console.error(error);
                alertaErrores(error);
            }
        }
    });
}

const permitirEditarTurno = (fecha) => {
    const partesFecha = fecha.split(' ');
    const fechaParte = partesFecha[0].split('-');
    const horaParte = partesFecha[1].split(':');

    const fecha_salida = new Date(
        parseInt(fechaParte[2]),
        parseInt(fechaParte[1]) - 1,
        parseInt(fechaParte[0]),
        parseInt(horaParte[0]),
        parseInt(horaParte[1]),
        parseInt(horaParte[2])
    );

    const fechaActual = new Date();
    const diferenciaMilisegundos = fechaActual - fecha_salida;
    const diferenciaHoras = diferenciaMilisegundos / (1000 * 60 * 60);

    return diferenciaHoras <= 48;
}
</script>

<template>
    <AuthenticatedLayout>
        <Banner class="pt-4" imagenUrl="/imagenes/banner-nuevo.png" titulo="Entrega de turno">
        </Banner>
        <div class="py-2">
            <div class="w-6/6 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-2xl sm:rounded-lg">
                    <div>
                        <div class="p-6 bg-white border-gray-200 mt-6">
                            <router-link :to="{ name: 'Inicio' }"
                                class="text-white bg-naranjo-light hover:bg-naranjo-dark focus:ring-4  font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">
                            Volver
                            </router-link>
                        </div>
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex flex-col">
                                <div>
                                    <div class="text-center pb-2">
                                        <Label class="text-2xl text-gris-dark">{{ titulo }}</Label>
                                    </div>
                                    <div class="float-right">
                                        <div class="mb-2">
                                            <input type="text" v-model="search" placeholder="Buscar..."
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-60 p-2.5" />
                                        </div>
                                    </div>
                                    <!-- Aqui va la tabla  -->
                                    <div class="min-w-full border-b shadow overflow-x-auto">
                                        <table class="w-full whitespace-nowrap rounded-lg overflow-hidden shadow-lg">
                                            <!-- Aqui esta el thead  -->
                                            <thead class="bg-[#DCE3FF]">
                                                <tr>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Código</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Fecha</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Médico Residente</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Médico Recibe</Label>
                                                    </th>
                                                    <th class="py-2 px-4 border-b border-grey-light text-left">
                                                        <Label class="font-bold text-gris-dark">Acciones</Label>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <!-- Aqui esta el tbody  -->
                                            <tbody>
                                                <tr v-for="turno in props.turnos.data" :key="turno.id_cambio_turno">
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">{{ turno.id_cambio_turno }}</Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <span class="text-gris-dark">
                                                                <strong class="mr-1">Entrada:</strong> {{ turno.fecha_llegada }} -
                                                                <strong class="ml-1 mr-1">Salida:</strong> {{ turno.fecha_salida }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">
                                                                {{ turno.medico_entrega?.nombre1_prof }}
                                                                {{ turno.medico_entrega?.apepat_prof }}
                                                                {{ turno.medico_entrega?.apemat_prof }}
                                                            </Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="px-2">
                                                            <Label class="text-gris-dark">
                                                                {{ turno.medico_recibe?.nombre1_prof }}
                                                                {{ turno.medico_recibe?.apepat_prof }}
                                                                {{ turno.medico_recibe?.apemat_prof }}
                                                            </Label>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]">
                                                        <div class="inline-flex gap-2">
                                                            <IconIr title="Ver" @click="detalle_turno(turno.id_cambio_turno)" />
                                                            <IconEdit v-if="permitirEditarTurno(turno.fecha)"
                                                                title="Editar"
                                                                class="focus:outline-none text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm py-2 mr-2 mb-2 dark:focus:ring-yellow-900"
                                                                @click="editar_turno(turno.id_cambio_turno)">
                                                            </IconEdit>
                                                            <IconEliminar v-if="permitirEditarTurno(turno.fecha)"
                                                                title="Eliminar"
                                                                @click="borrar_turno(turno.id_cambio_turno)">
                                                            </IconEliminar>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div v-if="!props.turnos.data" class="w-full">
                                            <div class="px-2 border-y">
                                                <div class="px-4 flex justify-center items-center text-center">
                                                    No se encontraron turnos entregados.
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <Pagination v-if="props.turnos.data" class="mt-6" :data="props.turnos" /> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <FooterInc />
    </AuthenticatedLayout>
</template>
