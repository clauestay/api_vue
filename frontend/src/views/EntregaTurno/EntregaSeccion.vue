<script setup>
import Label from "@/components/InputLabel.vue";
import Input from "@/components/TextInput.vue";
import TextArea from "@/components/TextArea.vue";
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

const sinInfo = "Sin información";

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
    ubicacion: [],
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
      const response = await axios.get(`/entrega-turno/obtenerInfoPaciente/${rut}`);
      let data = response.data.info_paciente;
      if (data != null) {
        props.form.entregados[index].nombre = data.nombre_completo;
        props.form.entregados[index].diagnostico = data.diagnostico;
        props.form.entregados[index].ubicacion = data.ubicacion;
        props.form.errors[`entregados.${index}.run`] = "";
      }
    } catch (err) {
      console.error({ err });
      const responseData = err.response?.data;
      if (responseData) {
        if (err.response.status === 404) {
          console.log(responseData.error);
          props.form.errors[`entregados.${index}.run`] = [responseData.error];
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
    props.form.errors[`entregados.${index}.run`] = "El run no es valido.";
  }
};

if (props.updating) {
  onMounted(() => {
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
        <i class="pi pi-plus cursor-pointer hover:animate-pulse" style="font-size: 1rem;"></i>
        </PrimaryButton>
      </div>
    </div>
    <div v-if="props.form?.entregados.length > 0" class="flex flex-col pt-2">
      <div
        class="grid grid-cols-5 gap-4 p-2"
        style="grid-template-columns: 2fr 2fr 2fr 2fr 1fr"
      >
        <div><Label class="font-black" for="run" value="Run" /></div>
        <div><Label class="font-black" for="datos_paciente" value="Datos paciente" /></div>
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
        <div
          class="grid grid-cols-5 gap-4 p-2"
          style="grid-template-columns: 2fr 2fr 2fr 2fr 1fr"
        >
          <div class="flex flex-col md:flex-row">
            <div v-if="!entregado.editable" class="flex flex-col">
              {{ entregado.run }}
            </div>
            <div v-else class="flex flex-col md:flex-row md:items-center">
              <div class="w-full md:w-3/4">
                <Input
                  :id="`entregado.${index}.run`"
                  v-model="props.form.entregados[index].run"
                  :class="{
                    'border-red-400':
                      props.form.errors?.[`entregados.${index}.run`] ||
                      props.form.errors?.[`entregados.${index}.v_run`],
                  }"
                  placeholder="Run ej: xxxxxxxx-x"
                  :aria-label="`props.form.entregados.${index}.run`"
                  @input="
                    props.formatearRut(
                      entregado.run,
                      props.form.entregados,
                      index,
                      'entregados'
                    );
                    props.form
                      .touch('entregados')
                      .validate(`entregados.${index}.run`);
                  "
                  type="text"
                  class="mt-1 block w-full"
                  autofocus
                />
                <Input
                  type="hidden"
                  :id="`entregado.${index}.v_run`"
                  v-model="props.form.entregados[index].v_run"
                  @input="
                    props.form
                      .touch('entregados')
                      .validate(`entregados.${index}.v_run`)
                  "
                />
                <InputError
                  class="mt-2"
                  v-if="props.form.invalid(`entregados.${index}.run`)"
                  :message="props.form.errors?.[`entregados.${index}.run`]"
                />
                <InputError
                  class="mt-2"
                  v-if="props.form.invalid(`entregados.${index}.v_run`)"
                  :message="props.form.errors?.[`entregados.${index}.v_run`]"
                />
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
            <div><b>Nombre:</b> {{ entregado.nombre ?? sinInfo }}</div>
            <div><b>Diagnostico:</b> {{ entregado.diagnostico ?? sinInfo }}</div>
            <div>
              <div v-if="entregado.ubicacion && entregado.ubicacion.length > 0">
                <div v-for="(ubicacion, uIndex) in entregado.ubicacion" :key="uIndex">
                  <b>Ubicacion: </b>
                  <b>Unidad:</b> {{ ubicacion.destino }}<br>
                  <b>Pieza:</b> {{ ubicacion.cod_pieza }}
                  <b>Cama:</b> {{ ubicacion.cod_cama }}<br>
                </div>
              </div>
              <div v-else>
                <b>Ubicación:</b> {{ sinInfo }}
              </div>
            </div>
          </div>
          <div class="flex flex-col">
            <TextArea
              :id="`entregado.${index}.problemas`"
              v-model="props.form.entregados[index].problemas"
              @input="
                props.form
                  .touch('entregados')
                  .validate(`entregados.${index}.problemas`)
              "
              :class="{
                'border-red-400':
                  props.form.errors?.[`entregados.${index}.problemas`],
              }"
              rows="3"
              cols="3"
              placeholder="Problemas / intervenciones"
              type="text"
              class="mt-1 block w-full"
              autofocus
            />
            <InputError
              v-if="props.form.invalid(`entregados.${index}.problemas`)"
              class="mt-2"
              :message="props.form.errors?.[`entregados.${index}.problemas`]"
            />
          </div>
          <div class="flex flex-col">
            <TextArea
              :id="`entregados.${index}.examenes`"
              v-model="props.form.entregados[index].examenes"
              @input="
                props.form
                  .touch('entregados')
                  .validate(`entregados.${index}.examenes`)
              "
              :class="{
                'border-red-400':
                  props.form.errors?.[`entregados.${index}.examenes`],
              }"
              type="text"
              rows="3"
              cols="3"
              placeholder="Examenes o Procedimientos Pendientres"
              class="mt-1 block w-full"
              autofocus
            />
            <InputError
              v-if="props.form.invalid(`entregados.${index}.examenes`)"
              class="mt-2"
              :message="props.form.errors?.[`entregados.${index}.examenes`]"
            />
          </div>
          <div class="flex justify-end">
            <IconEliminar @click.prevent="quitarLineaEntrega(entregado.id)" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
