<script setup>
import { ref, onMounted, computed} from "vue";
import { useRouter } from "vue-router";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Banner from "@/components/Banner.vue";
import FooterInc from "@/components/FooterInc.vue";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import "@bhplugin/vue3-datatable/dist/style.css";
import { reactive } from "vue";
import { useEntregaTurnoStore } from "@/stores/entregaTurno";

const entregaTurnoStore = useEntregaTurnoStore();

onMounted(() => {
  entregaTurnoStore.getListadoTurnos();
});

const turnos = computed(() => entregaTurnoStore.turnos);
const loading = computed(() => entregaTurnoStore.loading);
const total_rows = computed(() => entregaTurnoStore.total_rows);

const params = reactive({
  current_page: 1,
  pagesize: 10,
  search: "",
  sort_column: "ID_CAMBIO_TURNO",
  sort_direction: "desc",
});

const cols =
  ref([
    {
      field: "id_cambio_turno",
      title: "Código",
      isUnique: true,
      type: "number",
    },
    { field: "fecha", title: "Fecha", type: "date" },
    {
      field: "medico_entrega.nombre1_prof",
      title: "Medico Residente",
      search: true,
      sort: false,
      cellRenderer: (item) => {
        return (
          item &&
          item.medico_entrega?.nombre1_prof +
            " " +
            item.medico_entrega?.apepat_prof +
            " " +
            item.medico_entrega?.apemat_prof
        );
      },
    },
    {
      field: "medico_recibe.nombre1_prof",
      title: "Medico Recibe",
      search: true,
      sort: false,
      cellRenderer: (item) => {
        return (
          item &&
          item.medico_recibe?.nombre1_prof +
            " " +
            item.medico_recibe?.apepat_prof +
            " " +
            item.medico_recibe?.apemat_prof
        );
      },
    },
    { field: "actions", title: "Acciones", filter: false, sort: false },
  ]) || [];

const changeServer = (data) => {
  params.current_page = data.current_page;
  params.pagesize = data.pagesize;
  params.sort_column = data.sort_column;
  params.sort_direction = data.sort_direction;
  params.search = data.search;

  entregaTurnoStore.getListadoTurnos();
};

const router = useRouter();

const detalle_turno = (id_turno) => {
  router.push({ name: "Detalle-turno", params: { id: id_turno } });
};

</script>

<template>
  <AuthenticatedLayout>
    <Banner
      class="pt-4"
      imagenUrl="/imagenes/banner-nuevo.png"
      titulo="Entrega de turno"
    >
    </Banner>
    <br />
    <div class="py-2">
      <div class="w-5/6 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-2xl sm:rounded-lg">
          <div>
            <div class="p-6 bg-white border-gray-200 mt-6">
              <router-link
                :to="{ name: 'Inicio' }"
                class="text-white bg-naranjo-light hover:bg-naranjo-dark focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2"
              >
                Volver
              </router-link>
            </div>
            <div class="p-6 bg-white border-b border-gray-200">
              <div class="flex flex-col">
                <div>
                  <div class="text-center pb-2">
                  </div>
                  <div class="min-w-full border-b shadow overflow-x-auto">
                    <div class="mb-5">
                      <input
                        v-model="params.search"
                        type="text"
                        placeholder="Buscar..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-60 p-2.5"
                      />
                    </div>
                    <div class="row">
                      <div class="datatable-responsive">
                        <vue3-datatable
                          :rows="turnos"
                          :columns="cols"
                          :loading="loading"
                          :totalRows="total_rows"
                          :isServerMode="true"
                          :pageSize="params.pagesize"
                          :search="params.search"
                          :sortable="true"
                          :sortColumn="params.sort_column"
                          :sortDirection="params.sort_direction"
                          class="advanced-table whitespace-nowrap"
                          paginationInfo="Mostrando {0} de {1} de {2}"
                          noDataComponent="No se encontraron resultados."
                          @change="changeServer"
                        >
                        <template #id="data">
                          <strong>#{{ data.value.id_cambio_turno }}</strong>
                        </template>
                          <template #actions="data">
                            <div class="flex gap-4">
                              <div
                                class="inline-flex gap-2"
                                @click="detalle_turno(data.value.id_cambio_turno)"
                              >
                                <IconIr title="Ver" />
                              </div>
                            </div>
                          </template>
                        </vue3-datatable>
                      </div>
                    </div>
                    <!-- <table
                      class="w-full whitespace-nowrap rounded-lg overflow-hidden shadow-lg"
                    >
                      <thead class="bg-[#DCE3FF]">
                        <tr>
                          <th
                            class="py-2 px-4 border-b border-grey-light text-left"
                          >
                            <Label class="font-bold text-gris-dark"
                              >Código</Label
                            >
                          </th>
                          <th
                            class="py-2 px-4 border-b border-grey-light text-left"
                          >
                            <Label class="font-bold text-gris-dark"
                              >Fecha</Label
                            >
                          </th>
                          <th
                            class="py-2 px-4 border-b border-grey-light text-left"
                          >
                            <Label class="font-bold text-gris-dark"
                              >Médico Residente</Label
                            >
                          </th>
                          <th
                            class="py-2 px-4 border-b border-grey-light text-left"
                          >
                            <Label class="font-bold text-gris-dark"
                              >Médico Recibe</Label
                            >
                          </th>
                          <th
                            class="py-2 px-4 border-b border-grey-light text-left"
                          >
                            <Label class="font-bold text-gris-dark"
                              >Acciones</Label
                            >
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="turno in turnos.data"
                          :key="turno.id_cambio_turno"
                        >
                          <td
                            class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]"
                          >
                            <div class="px-2">
                              <Label class="text-gris-dark">{{
                                turno.id_cambio_turno
                              }}</Label>
                            </div>
                          </td>
                          <td
                            class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]"
                          >
                            <div class="px-2">
                              <span class="text-gris-dark">
                                <strong class="mr-1">Entrada:</strong>
                                {{ turno.fecha_llegada }} -
                                <strong class="ml-1 mr-1">Salida:</strong>
                                {{ turno.fecha_salida }}
                              </span>
                            </div>
                          </td>
                          <td
                            class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]"
                          >
                            <div class="px-2">
                              <Label class="text-gris-dark">
                                {{ turno.medico_entrega?.nombre1_prof }}
                                {{ turno.medico_entrega?.apepat_prof }}
                                {{ turno.medico_entrega?.apemat_prof }}
                              </Label>
                            </div>
                          </td>
                          <td
                            class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]"
                          >
                            <div class="px-2">
                              <Label class="text-gris-dark">
                                {{ turno.medico_recibe?.nombre1_prof }}
                                {{ turno.medico_recibe?.apepat_prof }}
                                {{ turno.medico_recibe?.apemat_prof }}
                              </Label>
                            </div>
                          </td>
                          <td
                            class="py-2 px-4 border-b border-grey-light text-left border-y border-[#DCE3FD]"
                          >
                            <div class="inline-flex gap-2">
                              <router-link
                                :to="{
                                  path: 'editar/' + turno.id_cambio_turno,
                                }"
                              >
                                <IconIr title="Ver" />
                              </router-link>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table> -->
                    <!-- <div v-if="!props.turnos.data" class="w-full">
                                            <div class="px-2 border-y">
                                                <div class="px-4 flex justify-center items-center text-center">
                                                    No se encontraron turnos entregados.
                                                </div>
                                            </div>
                                        </div> -->
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
