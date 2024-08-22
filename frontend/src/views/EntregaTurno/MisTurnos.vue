<script setup>
import { ref, computed, onMounted, watchEffect } from "vue";
import { useRouter } from "vue-router";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Banner from "@/components/Banner.vue";
import FooterInc from "@/components/FooterInc.vue";
import IconIr from "@/components/icons/IconIr.vue";
import IconEdit from "@/components/icons/IconEdit.vue";
import IconEliminar from "@/components/icons/IconEliminar.vue";
import Label from "@/components/InputLabel.vue";
import { Head, useHead } from "@vueuse/head";
import { useFetch } from "@/composables/fetch";
import jszip from "jszip";
import pdfmake from "pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfmake.vfs = pdfFonts.pdfMake.vfs;
import DataTable from "datatables.net-vue3";
import DataTablCore from "datatables.net-dt";
import "datatables.net-buttons-dt";
import "datatables.net-buttons/js/buttons.html5";
import "datatables.net-buttons/js/buttons.print";
import "datatables.net-responsive-dt";
DataTable.use(DataTablCore);
DataTablCore.Buttons.jszip(jszip);
DataTablCore.Buttons.pdfMake(pdfmake);
import ProgressSpinner from 'primevue/progressspinner';

useHead({ title: "Mis turnos" });

const { data, error, loading, fetchData /*, borrarRegistro*/ } = useFetch("/misTurnos");

const turnos = computed(() => data.value?.data?.turnos?.data || []);

onMounted(async () => {
  await fetchData();
});

const router = useRouter();

const cols =
  ref([
    {
      data: "id_cambio_turno",
      title: "Código",
    },
    {
      data: "fecha",
      title: "Fecha",
      type: "date",
    },
    {
      data: "medico_entrega.sta_descripcion",
      title: "Medico Residente",
    },
    {
      data: "medico_recibe.sta_descripcion",
      title: "Medico Recibe",
      // render: (data, type, row) => {
      //   return (
      //     row.medico_recibe.nombre1_prof +
      //     " " +
      //     row.medico_recibe.apepat_prof +
      //     " " +
      //     row.medico_recibe.apemat_prof
      //   );
      // },
    },
    {
      data: null,
      title: "Acciones",
      render: "#action",
    },
  ]) || [];

const options = ref({
  dom: '<"custom-datatable-header"lBf>rt<"custom-datatable-footer"ip>',
  responsive: true,
  order: [[0, "desc"]],
  autowidth: false,
  language: {
    search: "Buscar:",
    lengthMenu: "Mostrar _MENU_ registros",
    zeroRecords: "No se encontraron resultados",
    info: "Mostrando página _START_ de _END_ de _TOTAL_ registros",
    infoFiltered: "(filtrado de _MAX_ total de registros)",
  },
  buttons: [
    {
      extend: "excel",
      text: "Excel",
      className: "green",
      exportOptions: {
        columns: [0, 1, 2, 3],
      },
    },
    {
      extend: "csv",
      text: "Csv",
      exportOptions: {
        columns: [0, 1, 2, 3],
      },
    },
    {
      extend: "pdf",
      text: "Pdf",
      exportOptions: {
        columns: [0, 1, 2, 3],
      },
    },
    {
      extend: "print",
      text: "Vista impresión",
      exportOptions: {
        columns: [0, 1, 2, 3],
      },
    },
  ],
});

const detalle_turno = (id_turno) => {
  router.push({ name: "Detalle-turno", params: { id: id_turno } });
};

const editar_turno = (id_turno) => {
  router.push(`/editar/${id_turno}`);
};

const permitirEditarTurno = (fecha) => {
  const partesFecha = fecha.split(" ");
  const fechaParte = partesFecha[0].split("-");
  const horaParte = partesFecha[1].split(":");

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
};

// const borrar_turno = async (id) => {
//   const url = `/entregaTurno/borrarTurno/${id}`;
//   await borrarRegistro(url);
// };
</script>

<style>
@import "datatables.net-dt";
@import "datatables.net-buttons-dt";
@import "datatables.net-responsive-dt";

.custom-datatable-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.custom-datatable-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>

<template>
  <AuthenticatedLayout>
    <Head />
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
                    <Label class="text-2xl text-gris-dark">Mis Turnos</Label>
                  </div>
                  <div v-if="loading" class="flex justify-center items-center">
                    <ProgressSpinner />
                  </div>
                  <div v-else>
                    <DataTable
                      :columns="cols"
                      :data="turnos"
                      :options="options"
                      class="display nowrap"
                    >
                      <template #action="data">
                        <div class="flex gap-4">
                          <div
                            class="inline-flex gap-2"
                            @click="
                              detalle_turno(data.cellData.id_cambio_turno)
                            "
                          >
                            <IconIr title="Ver" />
                          </div>
                          <!-- v-if="permitirEditarTurno(data.cellData.fecha)" -->
                          <div
                            class="inline-flex gap-2 bg-yellow-400"
                            @click="editar_turno(data.cellData.id_cambio_turno)"
                          >
                            <IconEdit title="Editar" />
                          </div>
                          <!-- <div
                            v-if="permitirEditarTurno(data.cellData.fecha)"
                            class="inline-flex gap-2"
                            @click="
                              borrar_turno(
                                data.cellData.id_cambio_turno
                              )
                            "
                          >
                            <IconEliminar title="Eliminar" />
                          </div> -->
                        </div>
                      </template>
                    </DataTable>
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
