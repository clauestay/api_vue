<script setup>
import { ref, reactive, watch, onMounted, h } from "vue";
import { useRouter } from "vue-router";
import debounce from "lodash/debounce";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Banner from "@/components/Banner.vue";
import FooterInc from "@/components/FooterInc.vue";
import Pagination from "@/components/Pagination.vue";
import IconIr from "@/components/icons/IconIr.vue";
import IconEdit from "@/components/icons/IconEdit.vue";
import IconEliminar from "@/components/icons/IconEliminar.vue";
import Label from "@/components/InputLabel.vue";
import {
  alertaExito,
  alertaError,
  alertaErrores,
} from "@/helpers/AlertasSweetAlert";
import Swal from "sweetalert2";
import { Head, useHead } from "@vueuse/head";
import { useAuthStore } from "@/stores/auth";
import NProgress from "nprogress";

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

const authStore = useAuthStore();
axios.defaults.headers.common["Authorization"] =
  "Bearer " + authStore.authToken;

useHead({ title: "Mis turnos" });

const router = useRouter();

const turnos = ref([]);

const error = ref(null);
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
      data: "medico_entrega.nombre1_prof",
      title: "Medico Residente",
      render: (data, type, row) => {
        return (
          row.medico_entrega.nombre1_prof +
          " " +
          row.medico_entrega.apepat_prof +
          " " +
          row.medico_entrega.apemat_prof
        );
      },
    },
    {
      data: "medico_recibe.nombre1_prof",
      title: "Medico Recibe",
      render: (data, type, row) => {
        return (
          row.medico_recibe.nombre1_prof +
          " " +
          row.medico_recibe.apepat_prof +
          " " +
          row.medico_recibe.apemat_prof
        );
      },
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

const getMisTurnos = async () => {
  NProgress.start();
  try {
    const response = await axios.get("/misTurnos");

    console.log(response.data);
    turnos.value = response.data.turnos;
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
  NProgress.done();
};

const detalle_turno = (id_turno) => {
  router.push({ name: "Detalle-turno", params: { id: id_turno } });
};

const editar_turno = (id_turno) => {
  router.push({ name: "Editar", params: { id: id_turno } });
};

const borrar_turno = (id_turno) => {
  Swal.fire({
    title: "¿Desea eliminar el turno?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Confirmar",
    confirmButtonColor: "green",
    denyButtonText: `Cancelar`,
    cancelButtonColor: "red",
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        const response = await axios.delete(
          "entregaTurno.borrarTurno",
          id_turno
        );
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

onMounted(() => {
  getMisTurnos();
});
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
      <div class="w-6/6 mx-auto sm:px-6 lg:px-8">
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
                  <DataTable
                    :columns="cols"
                    :data="turnos.data"
                    :options="options"
                    class="display nowrap"
                  >
                    <template #action="data">
                      <div class="flex gap-4">
                        <div
                          class="inline-flex gap-2"
                          @click="detalle_turno(data.cellData.id_cambio_turno)"
                        >
                          <IconIr title="Ver" />
                        </div>
                        <div
                          v-if="permitirEditarTurno(data.cellData.fecha)"
                          class="inline-flex gap-2"
                          @click="editar_turno(data.cellData.id_cambio_turno)"
                        >
                          <IconEdit title="Editar" />
                        </div>
                        <div
                          v-if="permitirEditarTurno(data.cellData.fecha)"
                          class="inline-flex gap-2"
                          @click="borrar_turno(data.cellData.id_cambio_turno)"
                        >
                          <IconEliminar title="Eliminar" />
                        </div>
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
    <FooterInc />
  </AuthenticatedLayout>
</template>
