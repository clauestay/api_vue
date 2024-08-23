<script setup>
import Label from "@/components/InputLabel.vue";
import Input from "@/components/TextInput.vue";
import InputError from "@/components/InputError.vue";
import PrimaryButton from "@/components/PrimaryButton.vue";
import IconBuscar from "@/components/icons/IconBuscar.vue";
import IconEliminar from "@/components/icons/IconEliminar.vue";
import { validate } from "rut.js";
import { onMounted, defineProps, defineEmits } from "vue";
import { alertaError } from "@/helpers/AlertasSweetAlert";

const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
  formatearRut: {
    type: Function,
    required: true,
  },
  limpiarRut: {
    type: Function,
    required: true,
  },
  updating: {
    type: Boolean,
    required: false,
    default: false,
  },
});

const emit = defineEmits(["submit"]);

// Función para obtener un correlativo único
const correlativoFallecidos = () => new Date().getTime();

// Función para agregar una línea de fallecido
const agregarLineaFallecido = () => {
  props.form.fallecidos.push({
    id: correlativoFallecidos(),
    run: "",
    v_run: "",
    nombre: "",
    diagnostico: "",
    fecha_fallecido: "",
    editable: true,
    error: {},
  });
};

// Función para quitar una línea de fallecido
const quitarLineaFallecido = (id) => {
  if (props.form.fallecidos.length > 0) {
    const index = props.form.fallecidos.findIndex((f) => f.id === id);
    if (index !== -1) {
      props.form.fallecidos.splice(index, 1);
    }
  }
};

// Función para buscar información del paciente fallecido por RUT
const buscarInfoPacienteFallecido = async (rut, index) => {
  if (validate(rut)) {
    rut = props.limpiarRut(rut);
    try {
      const response = await axios.get(`/entrega-turno/obtenerInfoPaciente/${rut}`);
      let data = response.data.info_paciente;
      if (data != null) {
        props.form.fallecidos[index].nombre = data.nombre_completo;
        props.form.fallecidos[index].diagnostico = data.diagnostico;
        props.form.errors[`fallecidos.${index}.run`] = "";
      }
    } catch (err) {
      console.error({ err });
      const responseData = err.response?.data;
      if (responseData) {
        if (err.response.status === 404) {
          console.log(responseData.error);
          props.form.errors[`fallecidos.${index}.run`] = [responseData.error];
          alertaError(responseData.error);
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
    }
  } else {
    props.form.errors[`fallecidos.${index}.run`] = "El run no es valido.";
  }
};

if (props.updating) {
  onMounted(() => {
    props.form.fallecidos.forEach((fallecido, index) => {
      props.formatearRut(
        fallecido.run,
        props.form.fallecidos,
        index,
        "fallecidos"
      );
      buscarInfoPacienteFallecido(fallecido.run, index);
    });
  });
}
</script>

<template>
  <div class="bg-slate-600 mt-8 p-4 overflow-hidden shadow-xl sm:rounded-xl">
    <div class="flex justify-between items-center">
      <div class="flex-1 text-center">
        <Label
          class="text-xl flex justify-center underline mb-4 text-white"
          for="tituloFallecido"
          value="Pacientes fallecidos"
        />
      </div>
      <div>
        <PrimaryButton
          class="bg-green-600 hover:bg-green-700"
          title="agregar paciente fallecido."
          @click.prevent="agregarLineaFallecido"
        >
        <i class="pi pi-plus cursor-pointer hover:animate-pulse" style="font-size: 1rem;"></i>
        </PrimaryButton>
      </div>
    </div>
    <div v-if="props.form.fallecidos.length > 0" class="flex flex-col pt-2">
      <div
        class="grid grid-cols-5 gap-4 p-2"
        style="grid-template-columns: 2fr 2fr 2fr 2fr 1fr"
      >
        <div><Label class="font-black text-white" for="run" value="Run" /></div>
        <div>
          <Label class="font-black text-white" for="nombre" value="Nombre" />
        </div>
        <div>
          <Label
            class="font-black text-white"
            for="diagnostico"
            value="Diagnóstico"
          />
        </div>
        <div>
          <Label
            class="font-black text-white"
            for="fecha_fallecido"
            value="Fecha fallecido"
          />
        </div>
      </div>
    </div>
    <div class="flex flex-col">
      <div
        v-for="(fallecido, index) in props.form.fallecidos"
        :key="fallecido.id"
      >
        <hr class="my-2 border-t border-white" />
        <div
          class="grid grid-cols-5 gap-4 p-2"
          style="grid-template-columns: 2fr 2fr 2fr 2fr 1fr"
        >
          <div class="flex flex-col md:flex-row">
            <div v-if="!fallecido.editable" class="flex flex-col text-white">
              {{ fallecido.run }}
            </div>
            <div v-else class="flex flex-col md:flex-row md:items-center">
              <div class="w-full md:w-3/4">
                <Input
                  :id="`fallecido.${index}.run`"
                  v-model="props.form.fallecidos[index].run"
                  :class="{
                    'border-red-400': props.form.errors?.[`fallecidos.${index}.run`] || props.form.errors?.[`fallecidos.${index}.v_run`],
                  }"
                  placeholder="Run ej: xxxxxxxx-x"
                  :aria-label="`props.form.fallecidos.${index}.run`"
                  @input="
                    props.formatearRut(
                      fallecido.run,
                      props.form.fallecidos,
                      index,
                      'fallecidos'
                    );
                    props.form.touch('fallecidos').validate(`fallecidos.${index}.run`);
                  "
                  type="text"
                  class="mt-1 block w-full"
                  autofocus
                />
                <Input
                  type="hidden"
                  :id="`fallecido.${index}.v_run`"
                  v-model="props.form.fallecidos[index].v_run"
                  @input="props.form.touch('fallecidos').validate(`fallecidos.${index}.v_run`);"
                  />
                <InputError
                  class="mt-2"
                  v-if="props.form.invalid(`fallecidos.${index}.run`)"
                  textColorError="text-red-500"
                  :message="props.form.errors?.[`fallecidos.${index}.run`]"
                />
                <InputError
                  class="mt-2"
                  v-if="props.form.invalid(`fallecidos.${index}.v_run`)"
                  textColorError="text-red-500"
                  :message="props.form.errors?.[`fallecidos.${index}.v_run`]"
                />
              </div>
              <div class="mt-2 md:mt-0 md:w-1/4 md:ml-2">
                <PrimaryButton
                  v-if="validate(fallecido.run)"
                  class="bg-cyan-500 hover:bg-cyan-700"
                  @click.prevent="
                    buscarInfoPacienteFallecido(fallecido.run, index)
                  "
                >
                  <IconBuscar />
                </PrimaryButton>
              </div>
            </div>
          </div>
          <div class="flex flex-col">
            <div class="text-white">{{ fallecido.nombre }}</div>
          </div>
          <div class="flex flex-col">
            <div class="text-white">{{ fallecido.diagnostico }}</div>
          </div>
          <div class="flex flex-col">
            <Input
              :id="`fallecido.${index}.fecha_fallecido`"
              v-model="props.form.fallecidos[index].fecha_fallecido"
              @input="props.form.touch('fallecidos').validate(`fallecidos.${index}.fecha_fallecido`);"
              :class="{
                'border-red-400':
                  props.form.errors?.[`fallecidos.${index}.fecha_fallecido`],
              }"
              :aria-label="`props.form.fallecidos.${index}.fecha_fallecido`"
              type="datetime-local"
              class="mt-1 block w-full"
              autofocus
            />
            <InputError
              v-if="props.form.invalid(`fallecidos.${index}.fecha_fallecido`)"
              class="mt-2"
              textColorError="text-red-500"
              :message="
                props.form.errors?.[`fallecidos.${index}.fecha_fallecido`]
              "
            />
          </div>
          <div class="flex justify-end">
            <IconEliminar @click.prevent="quitarLineaFallecido(fallecido.id)" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
