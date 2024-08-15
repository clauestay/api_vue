<script setup>
import { watch } from "vue";
// import { useRouter } from "vue-router";
import FormSection from "@/components/FormSection.vue";
import EntregaSeccion from "@/views/EntregaTurno/EntregaSeccion.vue";
import FallecidosSeccion from "@/views/EntregaTurno/FallecidosSeccion.vue";
import TrasladosSeccion from "@/views/EntregaTurno/TrasladosSeccion.vue";
import CirugiasSeccion from "@/views/EntregaTurno/CirugiasSeccion.vue";
import Label from "@/components/InputLabel.vue";
import Input from "@/components/TextInput.vue";
import TextArea from "@/components/TextArea.vue";
import InputError from "@/components/InputError.vue";
import PrimaryButton from "@/components/PrimaryButton.vue";
import { validate, clean, format } from "rut.js";
import { minDate, maxDate } from "@/helpers/AyudaFechas.js";
import Select from "primevue/select";

const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
  medicos: {
    type: Object,
    required: true,
  },
  unidades: {
    type: Object,
    required: true,
  },
  errors: {
    type: Object,
    required: false,
  },
  updating: {
    type: Boolean,
    required: false,
    default: false,
  },
});

// comunicacion ascendente
const emit = defineEmits(["submit"]);

// Función para formatear el RUT
const formatearRut = (rut, arreglo, index, etiqueta) => {
  if (rut) {
    const run = format(rut);
    const v_run = limpiarRut(run);
    if (!validate(run)) {
      props.errors[`${etiqueta}.${index}.run`] = "El run no es valido.";
      limpiarInputs(arreglo, index, etiqueta);
    } else {
      arreglo[index].run = run;
      arreglo[index].v_run = v_run;
      props.errors[`${etiqueta}.${index}.run`] = "";
    }
  } else {
    limpiarInputs(arreglo, index, etiqueta);
  }
};

const limpiarRut = (rut) => {
  let run_limpio = clean(rut);
  rut = run_limpio.slice(0, -1);
  return rut;
};

const limpiarInputs = (arreglo, index, etiqueta) => {
  arreglo[index].v_run = "";
  arreglo[index].nombre = "";
  arreglo[index].diagnostico = "";

  if (etiqueta === "entregados") {
    arreglo[index].problemas = "";
    arreglo[index].examenes = "";
  }

  if (etiqueta === "fallecidos") {
    arreglo[index].fecha_fallecido = "";
  }

  if (etiqueta === "traslados") {
    arreglo[index].detalle.cod_unidad_origen = "";
    arreglo[index].detalle.pieza_origen = "";
    arreglo[index].detalle.cama_origen = "";
    arreglo[index].detalle.cod_unidad_destino = "";
    arreglo[index].detalle.pieza_destino = "";
    arreglo[index].detalle.cama_destino = "";
  }

  if (etiqueta === "cirugias") {
    arreglo[index].intervencion = "";
    arreglo[index].fecha_inicio = "";
    arreglo[index].fecha_fin = "";
  }
};

const validarTurnoExistente = (llegada, salida, medico_entrega) => {
  let data = {
    llegada: llegada,
    salida: salida,
    medico_entrega: medico_entrega,
  };

  axios
    .get("/comprobarTurnoExistente", { params: data })
    .then((resp) => {
      let response = resp.data.existeTurno;
      console.log({ data });
      if (response != null) {
        alertaPregunta(
          "Editar",
          response.msg,
          {  parametro: response.parametro }
        );
      }
    })
    .catch(function (error) {
      console.log({ error });
    });
};

if (!props.updating) {
  watch(
    [
      () => props.form.fecha_llegada,
      () => props.form.fecha_salida,
      () => props.form.medico_entrega,
    ],
    () => {
      if (
        props.form.fecha_llegada &&
        props.form.fecha_salida &&
        props.form.medico_entrega
      ) {
        validarTurnoExistente(
          props.form.fecha_llegada,
          props.form.fecha_salida,
          props.form.medico_entrega
        );
      }
    }
  );
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style>
  .disabled-style .p-dropdown {
    @apply bg-gray-200 text-gray-500 cursor-not-allowed;
  }
</style>


<template>
  {{ props.form }}
  <FormSection @submitted="$emit('submit')">
    <template #title>
      {{
        props.updating
          ? "Actualizar Entrega de turno"
          : "Ingresar Entrega de turno"
      }}
    </template>
    <template #form>
      <div
        class="bg-morado-light xl:mx-60 mt-8 p-4 overflow-hidden shadow-xl sm:rounded-lg"
      >
        <Label
          class="text-xl flex justify-center underline mb-4"
          for="tituloTraslados"
          value="Información turno"
        />
        <div class="flex flex-col">
          <div class="mx-10 md:flex mb-6">
            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
              <label for="medico_entrega">Entrada</label>
              <div class="flex items-center gap-2 2xl:mr-52">
                <Input
                  v-model="props.form.fecha_llegada"
                  placeholder=""
                  :class="{ 'border-red-400': props.errors?.fecha_llegada }"
                  :aria-label="props.form.fecha_llegada"
                  type="datetime-local"
                  :min="minDate"
                  :max="maxDate"
                  class="mt-1 block w-full"
                  autofocus
                />
              </div>
              <InputError class="mt-2" :message="props.errors?.fecha_llegada" />
            </div>
            <div class="md:w-1/2 px-3">
              <label for="medico_entrega">Salida</label>
              <div class="flex items-center gap-2 2xl:mr-52">
                <Input
                  v-model="props.form.fecha_salida"
                  placeholder=""
                  :class="{ 'border-red-400': props.errors?.fecha_salida }"
                  :aria-label="props.form.fecha_salida"
                  type="datetime-local"
                  :min="minDate"
                  :max="maxDate"
                  class="mt-1 block w-full"
                  autofocus
                />
              </div>
              <InputError class="mt-2" :message="props.errors?.fecha_salida?.[0]" />
            </div>
          </div>
          <div class="mx-10 md:flex mb-6">
            <div class="md:w-1/2 px-3">
              <label for="medico_entrega">Médico entrega</label>
              <Select
                :disabled="!updating"
                v-model="props.form.medico_entrega"
                :options="props.medicos"
                filter
                showClear
                optionLabel="name"
                placeholder="Buscar medico"
                class="w-full"
                :class="{ 'disabled-style': !updating }"
              >
                <template #value="slotProps">
                  <div v-if="slotProps.value" class="flex items-center">
                    <div>{{ slotProps.value.name }}</div>
                  </div>
                  <span v-else>
                    {{ slotProps.placeholder }}
                  </span>
                </template>
                <template #option="slotProps">
                  <div class="flex items-center">
                    <div>{{ slotProps.option.name }}</div>
                  </div>
                </template>
              </Select>
              <InputError class="mt-2" :message="props.errors?.medico_entrega?.[0]" />
            </div>
            <div class="md:w-1/2 px-3">
              <label for="medico_recibe">Médico recibe</label>
              <Select
                v-model="props.form.medico_recibe"
                :options="props.medicos"
                filter
                showClear
                optionLabel="name"
                placeholder="Buscar medico"
                class="w-full"
              >
                <template #value="slotProps">
                  <div v-if="slotProps.value" class="flex items-center">
                    <div>{{ slotProps.value.name }}</div>
                  </div>
                  <span v-else>
                    {{ slotProps.placeholder }}
                  </span>
                </template>
                <template #option="slotProps">
                  <div class="flex items-center">
                    <div>{{ slotProps.option.name }}</div>
                  </div>
                </template>
              </Select>
              <InputError class="mt-2" :message="props.errors?.medico_recibe?.[0]" />
            </div>
          </div>
        </div>
      </div>

      <EntregaSeccion
        :form="props.form"
        :formatearRut="formatearRut"
        :limpiarRut="limpiarRut"
        :updating="props.updating"
      />

      <!-- <FallecidosSeccion
        :form="form"
        :formatearRut="formatearRut"
        :limpiarRut="limpiarRut"
        :updating="props.updating"
      />

      <TrasladosSeccion
        :form="form"
        :unidades="props.unidades"
        :formatearRut="formatearRut"
        :limpiarRut="limpiarRut"
        :updating="props.updating"
      />

      <CirugiasSeccion
        :form="form"
        :formatear-rut="formatearRut"
        :limpiarRut="limpiarRut"
        :updating="props.updating"
      /> -->

      <div class="bg-gray-200 mt-8 p-4 overflow-hidden shadow-xl sm:rounded-lg">
        <div class="flex flex-col">
          <div class="mx-10 md:flex mb-6">
            <div class="md:w-full px-3">
              <div>
                <Label for="novedades" value="Ingrese Novedades del turno:" />
                <TextArea
                  v-model="props.form.novedades"
                  :class="{ 'border-red-400': props.errors?.novedades?.[0] }"
                  placeholder="Ingrese Novedades del turno"
                  type="text"
                  class="mt-1 block w-full"
                  autofocus
                />
                <InputError class="mt-2" :message="props.errors?.novedades?.[0]" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <template #actions>
      <div class="flex justify-end pt-4">
        <div class="float-right">
          <PrimaryButton
            class="bg-morado-base hover:bg-morado-dark"
            :class="{ 'opacity-25': props.form.processing }"
            :disabled="props.form.processing"
          >
            {{ props.updating ? "Actualizar" : "Guardar" }}
          </PrimaryButton>
        </div>
      </div>
    </template>
  </FormSection>
</template>
