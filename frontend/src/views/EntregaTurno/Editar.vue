<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Banner from "@/components/Banner.vue";
import EntregaTurnoForm from "@/views/EntregaTurno/Form.vue";
import FooterInc from "@/components/FooterInc.vue";
import Button from "primevue/button";
import { useHead } from "@vueuse/head";
import ProgressSpinner from "primevue/progressspinner";
import { useForm } from "laravel-precognition-vue";
import { useFetch } from "@/composables/fetch";
import { manejarError } from "@/functions";
import {
  alertaExito,
  alertaError,
  alertaErrores,
} from "@/helpers/AlertasSweetAlert";

useHead({ title: "Editar turno" });

const route = useRoute();
const id_turno = route.params.id;
const router = useRouter();
const loading = ref(true);

const {
  data: dataMedicos,
  error: medicoError,
  fetchData: medicoFetchData,
} = useFetch("/entrega-turno/medicosEntregaTurno");
const medicos = computed(() => dataMedicos.value?.data?.medicos || []);

const {
  data: dataUnidades,
  error: medicoUnidades,
  fetchData: unidadesFetchData,
} = useFetch("/entrega-turno/unidades");
const unidades = computed(() => dataUnidades.value?.data?.unidades || []);

const { data: dataTurno, fetchData: turnoFetchData } = useFetch(
  `/entrega-turno/obtenerTurno/${id_turno}`
);
const turno = computed(() => dataTurno.value?.data?.turno || []);

const { data: dataEntregados, fetchData: entregadosFetchData } = useFetch(
  `/entrega-turno/obtenerEntregados/${id_turno}`
);
const entregados = computed(() => dataEntregados.value?.data?.entregados || []);

const { data: dataTraslados, fetchData: trasladosFetchData } = useFetch(
  `/entrega-turno/obtenerTraslados/${id_turno}`
);
const traslados = computed(() => dataTraslados.value?.data?.traslados || []);

const { data: dataFallecidos, fetchData: fallecidosFetchData } = useFetch(
  `/entrega-turno/obtenerFallecidos/${id_turno}`
);
const fallecidos = computed(() => dataFallecidos.value?.data?.fallecidos || []);

const { data: dataCirugias, fetchData: cirugiasFetchData } = useFetch(
  `/entrega-turno/obtenerCirugias/${id_turno}`
);
const cirugias = computed(() => dataCirugias.value?.data?.cirugias || []);

onMounted(async () => {
  loading.value = true;
  try {
    await medicoFetchData();
    await unidadesFetchData();
    await turnoFetchData();
    await entregadosFetchData();
    await trasladosFetchData();
    await fallecidosFetchData();
    await cirugiasFetchData();
  } catch (error) {
    manejarError(error, "Error al obtener el turno");
  } finally {
    loading.value = false;
  }
});

const form = useForm("post", "/entrega-turno/actualizarCambioTurno", {
  id_turno: id_turno,
  entregados: [],
  traslados: [],
  fallecidos: [],
  cirugias: [],
  novedades: "",
  reemplazante: false,
  medico_entrega: {},
  medico_recibe: {},
  fecha_llegada: "",
  fecha_salida: "",
});

watch(turno, (newTurno) => {
  if (newTurno) {
    form.novedades = newTurno?.novedades || "";
    form.reemplazante = newTurno?.reemplazante || false;
    form.medico_entrega =
      {
        id: newTurno?.medico_entrega?.cod_prof,
        name: newTurno?.medico_entrega?.sta_descripcion,
      } || {};
    form.medico_recibe =
      {
        id: newTurno?.medico_recibe?.cod_prof,
        name: newTurno?.medico_recibe?.sta_descripcion,
      } || {};
    form.fecha_llegada = newTurno?.fecha_llegada || "";
    form.fecha_salida = newTurno?.fecha_salida || "";
  }
});

watch(entregados, (newEntregados) => {
  form.entregados = newEntregados;
});

watch(traslados, (newTraslados) => {
  form.traslados = newTraslados;
});

watch(fallecidos, (newFallecidos) => {
  form.fallecidos = newFallecidos;
});

watch(cirugias, (newCirugias) => {
  form.cirugias = newCirugias;
});

const goBack = () => {
  window.history.back();
};

const submit = () => {
  form
    .submit()
    .then((response) => {
      const responseData = response.data;
      if (responseData.exito) {
        alertaExito(responseData.exito);
        form.reset();
        router.push("/entrega-turno/misTurnos");
      } else if (responseData.error) {
        alertaError(responseData.error);
      }
    })
    .catch((error) => {
      console.error(error);
      const responseData = error.response?.data;

      if (responseData) {
        if (error.response.status === 409) {
          alertaError(responseData.info);
        } else if (responseData.errors) {
          alertaErrores(responseData.errors);
        } else if (responseData.error) {
          alertaError(responseData.error);
        } else {
          alertaError("Se ha producido un error desconocido.");
        }
      } else {
        alertaError(
          "Se ha producido un error en la red o un error inesperado."
        );
      }
    });
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
    <div class="py-4">
      <div class="w-5/6 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-4 overflow-hidden shadow-2xl sm:rounded-lg">
          <div class="px-0 py-6 bg-white border-gray-200 mt-6">
            <Button @click="goBack()" label="Volver" severity="warn"> </Button>
          </div>
          <div v-if="loading" class="flex justify-center items-center">
            <ProgressSpinner />
          </div>
          <div v-else>
            <EntregaTurnoForm
              :updating="true"
              :form="form"
              :medicos="medicos"
              :unidades="unidades"
              @submit="submit"
            />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
  <FooterInc></FooterInc>
</template>
