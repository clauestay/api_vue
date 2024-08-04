<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Banner from '@/Components/Banner.vue';
import FooterInc from '@/Components/FooterInc.vue';
// import { Head, Link } from '@inertiajs/vue3';
import { useAuthStore } from '@/store/auth';

const authStore = useAuthStore();
authStore.fetchUser();

const user = computed(() => authStore.user);
</script>

<template>
    <Head title="Entrega turno" />
    <AuthenticatedLayout>
        <Banner class="pt-4" imagenUrl="/imagenes/banner-nuevo.png" titulo="Entrega de turno">
        </Banner>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-3 md:grid-cols-3 py-14 px-14">
                    <div class="pt-4 px-5">
                        <div class="flex items-center">
                            <div class="overflow-hidden shadow-xl sm:rounded-lg">
                                <Link :href="route('entregaTurno.crear')"
                                    class="bg-naranjo-light-0 px-12 py-12
                                        inline-flex items-center border border-naranjo-dark rounded-md text-gris-dark text-center capitalized tracking-widest hover:bg-naranjo-dark hover:text-white focus:outline-none focus:ring disabled:opacity-25 transition">
                                Entrega de Turnos
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 px-5">
                        <div class="flex items-center">
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                                <Link :href="route('entregaTurno.misTurnos')"
                                    class="bg-naranjo-light-0 px-12 py-12
                                        inline-flex items-center border border-naranjo-dark rounded-md text-gris-dark text-center capitalized tracking-widest hover:bg-naranjo-dark hover:text-white focus:outline-none focus:ring disabled:opacity-25 transition">
                                Mis turnos
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 px-5">
                        <div class="flex items-center">
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                                <Link :href="route('entregaTurno.listadoTurnos')"
                                    class="bg-naranjo-light-0 px-12 py-12
                                        inline-flex items-center border border-naranjo-dark rounded-md text-gris-dark text-center capitalized tracking-widest hover:bg-naranjo-dark hover:text-white focus:outline-none focus:ring disabled:opacity-25 transition">
                                Listado de Turnos
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <FooterInc></FooterInc>
    </AuthenticatedLayout>
</template>
