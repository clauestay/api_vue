<script setup>
import { Head, useHead } from '@vueuse/head';
import { computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/components/Banner.vue';
import FooterInc from '@/components/FooterInc.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const user = computed(() => authStore.user);

useHead({
    title: 'Entrega turno',
});
</script>

<template>
    <AuthenticatedLayout>
        <Head/>
        <Banner class="pt-4" imagenUrl="/imagenes/banner-nuevo.png" titulo="Entrega de turno" />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-3 md:grid-cols-3 py-14 px-14">
                    <div class="pt-4 px-5">
                        <div class="flex items-center">
                            <div class="overflow-hidden shadow-xl sm:rounded-lg">
                                <router-link to="/crear"
                                    class="bg-naranjo-light-0 px-12 py-12
                                        inline-flex items-center border border-naranjo-dark rounded-md text-gris-dark text-center capitalized tracking-widest hover:bg-naranjo-dark hover:text-white focus:outline-none focus:ring disabled:opacity-25 transition">
                                Entrega de Turnos
                                </router-link>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 px-5">
                        <div class="flex items-center">
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                                <router-link to="/misTurnos"
                                    class="bg-naranjo-light-0 px-12 py-12
                                        inline-flex items-center border border-naranjo-dark rounded-md text-gris-dark text-center capitalized tracking-widest hover:bg-naranjo-dark hover:text-white focus:outline-none focus:ring disabled:opacity-25 transition">
                                Mis turnos
                                </router-link>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 px-5">
                        <div class="flex items-center">
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                                <router-link to="/listadoTurnos"
                                    class="bg-naranjo-light-0 px-12 py-12
                                        inline-flex items-center border border-naranjo-dark rounded-md text-gris-dark text-center capitalized tracking-widest hover:bg-naranjo-dark hover:text-white focus:outline-none focus:ring disabled:opacity-25 transition">
                                Listado de Turnos
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <FooterInc></FooterInc>
    </AuthenticatedLayout>
</template>
