<script setup>
import Label from "@/components/InputLabel.vue";
import Input from "@/components/TextInput.vue";
import TextArea from "@/components/TextArea.vue";
import InputError from "@/components/InputError.vue";
import PrimaryButton from "@/components/PrimaryButton.vue";
import IconBuscar from "@/components/icons/IconBuscar.vue";
import IconEliminar from "@/components/icons/IconEliminar.vue";
import { validate } from "rut.js";
import { ref, onMounted, defineProps, defineEmits } from "vue";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
axios.defaults.headers.common["Authorization"] = "Bearer " + authStore.authToken;

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
const correlativoEntregados = () => new Date().getTime();

// Función para agregar una línea de entrega
const agregarLineaEntrega = () => {
  props.form.entregados.push({
    id: correlativoEntregados(),
    run: "",
    v_run: "",
    nombre: "",
    diagnostico: "",
    problemas: "",
    examenes: "",
    editable: true,
    error: {},
  });
};

// Función para quitar una línea de entrega
const quitarLineaEntrega = (id) => {
  if (props.form.entregados.length > 0) {
    const index = props.form.entregados.findIndex((f) => f.id === id);
    if (index !== -1) {
      props.form.entregados.splice(index, 1);
    }
  }
};

// Función para buscar información del paciente por RUT
const buscarInfoPacienteRut = async (rut, index) => {
  if (validate(rut)) {
    rut = props.limpiarRut(rut);
    try {
      const response = await axios.get(`/obtenerInfoPaciente/${rut}`);
      let data = response.data.info_paciente;
      if (data != null) {
        props.form.entregados[index].nombre = data.nombre_completo;
        props.form.entregados[index].diagnostico = data.diagnostico;
      } else {
        props.form.errors[`entregados.${index}.run`] =
          "El run ingresado no existe en nuestros registros.";
      }
    } catch (error) {
      console.error({ error });
    }
  } else {
    props.form.entregados[index].error.run = "El run no es valido.";
  }
};

if (props.updating) {
  onMounted(() => {
    // Llamar a formatearRut para cada elemento de la lista
    console.log(props.form.entregados);
    props.form.entregados.forEach((entregado, index) => {
      props.formatearRut(
        entregado.run,
        props.form.entregados,
        index,
        "entregados"
      );
      buscarInfoPacienteRut(entregado.run, index);
    });
  });
}
</script>

<template>
  <div class="bg-blue-200 mt-8 p-4 overflow-hidden shadow-xl sm:rounded-lg">
    <div class="flex justify-between items-center">
      <div class="flex-1 text-center">
        <Label
          class="text-xl underline"
          for="tituloEntrega"
          value="Pacientes a entregar"
        />
      </div>
      <div>
        <PrimaryButton
          class="bg-green-600 hover:bg-green-700"
          title="agregar paciente que permanece hospitalizado."
          @click.prevent="agregarLineaEntrega"
        >
          +
        </PrimaryButton>
      </div>
    </div>

    <div v-if="props.form.entregados.length > 0" class="flex flex-col pt-2">
      <div class="grid grid-cols-6 gap-4 p-2" style="grid-template-columns: 2fr 2fr 2fr 2fr 2fr 1fr;">
        <div><Label class="font-black" for="run" value="Run" /></div>
        <div><Label class="font-black" for="nombre" value="Nombre" /></div>
        <div>
          <Label class="font-black" for="diagnostico" value="Diagnóstico" />
        </div>
        <div>
          <Label
            class="font-black"
            for="problemas"
            value="Problemas / intervenciones"
          />
        </div>
        <div>
          <Label
            class="font-black"
            for="examenes"
            value="Examenes o Procedimientos Pendientres"
          />
        </div>
      </div>
    </div>
    <div class="flex flex-col">
      <div
        v-for="(entregado, index) in props.form.entregados"
        :key="entregado.id"
      >
        <hr class="my-2 border-t border-gray-600" />
        <div class="grid grid-cols-6 gap-4 p-2" style="grid-template-columns: 2fr 2fr 2fr 2fr 2fr 1fr;">
          <div class="flex flex-col md:flex-row">
            <div v-if="!entregado.editable" class="flex flex-col">
              {{ entregado.run }}
            </div>
            <div v-else class="flex flex-col md:flex-row md:items-center">
              <div class="w-full md:w-3/4">
                <!-- :class="{ 'border-red-400': props.form.errors[`entregados.${index}.run`] }" -->
                <Input
                  v-model="entregado.run"
                  placeholder="Run ej: xxxxxxxx-x"
                  :aria-label="`props.form.entregado.${index}.run`"
                  @input="
                    props.formatearRut(
                      entregado.run,
                      props.form.entregados,
                      index,
                      'entregados'
                    )
                  "
                  type="text"
                  class="mt-1 block w-full"
                  autofocus
                />
                <Input type="hidden" v-model="entregado.v_run" />
                <!-- <InputError class="mt-2" :message="props.form.errors[`entregados.${index}.run`]" /> -->
                <!-- <InputError class="mt-2" :message="props.form.errors[`entregados.${index}.v_run`]" /> -->
              </div>
              <div class="mt-2 md:mt-0 md:w-1/4 md:ml-2">
                <PrimaryButton
                  v-if="validate(entregado.run)"
                  class="bg-cyan-500 hover:bg-cyan-700"
                  @click.prevent="buscarInfoPacienteRut(entregado.run, index)"
                >
                  <IconBuscar />
                </PrimaryButton>
              </div>
            </div>
          </div>
          <div class="flex flex-col">
            <div>{{ entregado.nombre }}</div>
          </div>
          <div class="flex flex-col">
            <div>{{ entregado.diagnostico }}</div>
          </div>
          <div class="flex flex-col">
            <!-- :class="{ 'border-red-400': props.form.errors[`entregados.${index}.problemas`] }" -->
            <TextArea
              v-model="entregado.problemas"
              rows="3"
              cols="3"
              placeholder="Problemas / intervenciones"
              type="text"
              class="mt-1 block w-full"
              autofocus
            />
            <!-- <InputError class="mt-2" :message="props.form.errors[`entregados.${index}.problemas`]" /> -->
          </div>
          <div class="flex flex-col">
            <!-- :class="{ 'border-red-400': props.form.errors[`entregados.${index}.examenes`] }" type="text" -->
            <TextArea
              v-model="entregado.examenes"
              rows="3"
              cols="3"
              placeholder="Examenes o Procedimientos Pendientres"
              class="mt-1 block w-full"
              autofocus
            />
            <!-- <InputError class="mt-2" :message="props.form.errors[`entregados.${index}.examenes`]" /> -->
          </div>
          <div class="flex justify-end">
            <IconEliminar @click.prevent="quitarLineaEntrega(entregado.id)" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
