<script setup>
import { watch } from "vue";
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
import { alertaPregunta } from "@/helpers/AlertasSweetAlert";

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
  updating: {
    type: Boolean,
    required: false,
    default: false,
  }
});

// comunicacion ascendente
const emit = defineEmits(["submit"]);

// Función para formatear el RUT
const formatearRut = (rut, arreglo, index, etiqueta) => {
  if (rut) {
    const run = format(rut);
    const v_run = limpiarRut(run);
    if (!validate(run)) {
      props.form.validate(`${etiqueta}.${index}.run`);
      limpiarInputs(arreglo, index, etiqueta);
    } else {
      arreglo[index].run = run;
      arreglo[index].v_run = v_run;
      props.form.validate(`${etiqueta}.${index}.v_run`);
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
    .get("/entrega-turno/comprobarTurnoExistente", { params: data })
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
                  id="fecha_llegada"
                  v-model="props.form.fecha_llegada"
                  :class="{ 'border-red-400': props.form.errors?.fecha_llegada }"
                  @change="props.form.validate('fecha_llegada')"
                  :aria-label="props.form.fecha_llegada"
                  type="datetime-local"
                  :min="!props.updating ?? minDate"
                  :max="!props.updating ?? maxDate"
                  class="mt-1 block w-full"
                  autofocus
                />
              </div>
              <InputError class="mt-2" v-if="props.form.invalid('fecha_llegada')" :message="props.form.errors?.fecha_llegada" />
            </div>
            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
              <label for="medico_entrega">Salida</label>
              <div class="flex items-center gap-2 2xl:mr-52">
                <Input
                  id="fecha_salida"
                  v-model="props.form.fecha_salida"
                  :class="{ 'border-red-400': props.form.errors?.fecha_salida }"
                  :change="props.form.validate('fecha_salida')"
                  :aria-label="props.form.fecha_salida"
                  type="datetime-local"
                  :min="!props.updating ?? minDate"
                  :max="!props.updating ?? maxDate"
                  class="mt-1 block w-full"
                  autofocus
                />
              </div>
              <InputError class="mt-2" v-if="props.form.invalid('fecha_salida')" :message="props.form.errors?.fecha_salida" />
            </div>
          </div>
          <div class="mx-10 md:flex mb-6">
            <div class="md:w-1/2 px-3">
              <label for="medico_entrega">Médico entrega</label>
              <Select
                id="medico_entrega"
                :disabled="!updating"
                v-model="props.form.medico_entrega"
                @change="props.form.validate('medico_entrega')"
                :options="props.medicos"
                filter
                checkmark
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
              <InputError class="mt-2" v-if="props.form.invalid('medico_entrega')" :message="props.form.errors?.medico_entrega" />
            </div>
            <div class="md:w-1/2 px-3">
              <label for="medico_recibe">Médico recibe</label>
              <Select
                id="medico_recibe"
                v-model="props.form.medico_recibe"
                :options="props.medicos"
                @change="props.form.validate('medico_recibe')"
                :invalid="props.form.medico_recibe === null"
                filter
                checkmark
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
              <InputError class="mt-2" v-if="props.form.invalid('medico_recibe')" :message="props.form.errors?.medico_recibe" />
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

      <FallecidosSeccion
        :form="props.form"
        :formatearRut="formatearRut"
        :limpiarRut="limpiarRut"
        :updating="props.updating"
      />

      <TrasladosSeccion
        :form="props.form"
        :unidades="props.unidades"
        :formatearRut="formatearRut"
        :limpiarRut="limpiarRut"
        :updating="props.updating"
      />

      <CirugiasSeccion
        :form="props.form"
        :formatear-rut="formatearRut"
        :limpiarRut="limpiarRut"
        :updating="props.updating"
      />

      <div class="bg-gray-200 mt-8 p-4 overflow-hidden shadow-xl sm:rounded-lg">
        <div class="flex flex-col">
          <div class="mx-10 md:flex mb-6">
            <div class="md:w-full px-3">
              <div>
                <Label for="novedades" value="Ingrese Novedades del turno:" />
                <TextArea
                  id="novedades"
                  v-model="props.form.novedades"
                  :class="{'border-red-600': props.form.errors?.novedades}"
                  @keyup="props.form.validate('novedades')"
                  placeholder="Ingrese Novedades del turno"
                  type="text"
                  class="mt-1 block w-full"
                  />
                <InputError class="mt-2" v-if="props.form.invalid('novedades')" :message="props.form.errors.novedades" />
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
