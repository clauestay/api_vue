<script setup>
import ManualPDF from '@/components/ManualPDF.vue';

  const props = defineProps({
    imagenUrl: {
      type: String,
      required: true,
    },
    titulo: {
      type: String,
      default: "Entrega de turno",
    }
  });

  const pdfManualEntregaTurno = "files/Manual-entrega-turnos.pdf";

</script>

<template>
  <div class="relative">
    <img :src="props.imagenUrl" class="bg-cover bg-center h-48 text-white object-fill w-full px-2" alt="banner de módulo entrega de turno">
    <span class="absolute bottom-8 left-8 text-4xl font-bold text-[#5B6670] px-2 py-1">{{ props.titulo }}</span>
  </div>
  <div class="py-6 px-6">
    <ManualPDF :pdfPath="pdfManualEntregaTurno"/>
  </div>
</template>