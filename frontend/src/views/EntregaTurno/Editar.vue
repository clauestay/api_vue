<script setup>
import { onMounted, computed } from 'vue';
import { useRoute, useRouter } from "vue-router";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import EntregaTurnoForm from '@/views/EntregaTurno/Form.vue';
import FooterInc from '@/components/FooterInc.vue';
import Button from 'primevue/button';
import { Head, useHead } from '@vueuse/head';
import dayjs from 'dayjs';
import ProgressSpinner from 'primevue/progressspinner';
import { useEntregaTurnoStore } from '@/stores/entregaTurno';

const route = useRoute();
const id_turno = route.params.id;

const router = useRouter();

const entregaTurnoStore = useEntregaTurnoStore();

onMounted(async() => {
    entregaTurnoStore.resetForm();
    entregaTurnoStore.getMedicos();
    await entregaTurnoStore.obtenerTurno(id_turno);
    entregaTurnoStore.form = {
        id_turno: id_turno,
        entregados: entregaTurnoStore.entregados,
        traslados: entregaTurnoStore.traslados,
        fallecidos: entregaTurnoStore.fallecidos,
        cirugias: entregaTurnoStore.cirugias,
        novedades: entregaTurnoStore.turno?.novedades || '',
        reemplazante: entregaTurnoStore.turno?.reemplazante || false,
        medico_entrega: { id: entregaTurnoStore.turno?.medico_entrega.cod_prof, name: entregaTurnoStore.turno?.medico_entrega.sta_descripcion } || '',
        medico_recibe: { id: entregaTurnoStore.turno?.medico_recibe.cod_prof, name: entregaTurnoStore.turno?.medico_recibe.sta_descripcion } || '',
        fecha_llegada: entregaTurnoStore.turno?.fecha_llegada || '',
        fecha_salida: entregaTurnoStore.turno?.fecha_salida || '',
    };
})

useHead({ title: "Editar turno" });

const goBack = () => {
    window.history.back();
};

const loading = computed(() => entregaTurnoStore.loading_obtener);
const errors = computed(() => entregaTurnoStore.errors);
const medicos = computed(() => entregaTurnoStore.medicos);
const unidades = computed(() => entregaTurnoStore.unidades);
const form = computed(() => entregaTurnoStore.form);

const submit = () => {
    entregaTurnoStore.actualizarCambioTurno(router);
}

</script>

<template>
    <AuthenticatedLayout>
        <Banner class="pt-4" imagenUrl="/imagenes/banner-nuevo.png" titulo="Entrega de turno">
        </Banner>
        <div class="py-4">
            <div class="w-6/6 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-4 overflow-hidden shadow-2xl sm:rounded-lg">
                    <div class="px-0 py-6 bg-white border-gray-200 mt-6">
                        <Button @click="goBack()"
                            label="Volver"
                            severity="warn">
                        </Button>
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
                            :errors="errors"
                            @submit="submit" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <FooterInc></FooterInc>
</template>
