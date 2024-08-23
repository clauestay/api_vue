<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Banner from "@/components/Banner.vue";
import PrimaryButton from '@/components/PrimaryButton.vue';
import FooterInc from "@/components/FooterInc.vue";
import { Head, useHead } from "@vueuse/head";
import { reactive } from "vue";
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
import ProgressSpinner from "primevue/progressspinner";

useHead({ title: "Listado de turnos" });

const { data, error, loading, fetchData } = useFetch("/entrega-turno/listadoTurnos");

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
                    <Label class="text-2xl text-gris-dark"
                      >Listado de Turnos</Label
                    >
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
                          <PrimaryButton
                            class="inline-flex gap-2 bg-blue-600 hover:bg-blue-800 rounded-3xl"
                            @click="
                              detalle_turno(data.cellData.id_cambio_turno)
                            "
                          >
                            <i class="pi pi-eye cursor-pointer hover:animate-pulse" style="font-size: 1.8rem;"></i>
                        </PrimaryButton>
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