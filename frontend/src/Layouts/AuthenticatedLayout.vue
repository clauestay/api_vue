<script setup>
import { ref, computed } from 'vue';
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import ResponsiveNavLink from '@/components/ResponsiveNavLink.vue';
// import { Link, usePage } from '@inertiajs/vue3';
import HoraActual from '@/components/HoraActual.vue';
import Label from '@/components/InputLabel.vue';
import { useAuthStore } from '@/stores/auth';

const showingNavigationDropdown = ref(false);

const authStore = useAuthStore();
const user = computed(() => authStore.user);
// const user = {nombre: 'nombre', apellido: 'apellido', email: 'email'};
const toggleDropdown = () => {
    showingNavigationDropdown.value = !showingNavigationDropdown.value;
};
</script>

<template>
    <div class="relative">
        <div class="min-h-screen bg-[#F2EFEF] pt-16">
            <nav class="bg-[#FBFBFB] border-b border-gray-100 fixed w-full top-0 z-10">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <router-link to="/inicio" class="text-red-500">
                                Instituto Nacional del Cáncer
                                </router-link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <a class="inline-flex items-center px-1 pt-1 border-b-2 text-blue-400 hover:text-gray-500"
                                    href="https://smnpacs5.synapsetimed.cl/Synapse">
                                    Synapse
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <a class="inline-flex items-center px-1 pt-1 border-b-2 text-blue-400 hover:text-gray-500"
                                    href="https://inccwm.synapsetimed.cl:8443/login.aspx">
                                    Ris
                                </a>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" @click="toggleDropdown"
                                                class="inline-flex items-center px-3 py-2 rounded-full border border-[#8FA1E6] text-sm leading-4 font-medium text-gray-500 bg-[#FBFBFB] hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                <svg class="w-6 h-6 text-[#8FA1E6] dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd"
                                                        d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                                                        clip-rule="evenodd" />
                                                </svg>

                                                <Label class="text-gris-dark"> {{ user.nombre }} {{ user.apellido }} </Label>

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- <DropdownLink :href="route('profile.edit')"> Perfil </DropdownLink> -->
                                        <!-- <DropdownLink :href="route('logout')" method="post" as="button">
                                            Cerrar sesión
                                        </DropdownLink> -->
                                        <DropdownLink to="/logout" method="post" as="button"> Cerrar sesión </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                            <HoraActual class="px-2" />
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="toggleDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <!-- <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink> -->
                    <ResponsiveNavLink href="/inicio" active="/inicio" >
                        Dashboard
                    </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-red-500">
                                {{ user.nombre }} {{ user.apellido }}
                            </div>
                            <div class="font-medium text-sm text-red-500">
                                {{ user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <!-- <ResponsiveNavLink :href="route('profile.edit')"> Perfil </ResponsiveNavLink> -->
                            <!-- <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Cerrar sesión
                            </ResponsiveNavLink> -->
                            <router-link to="/logout" method="post" as="button"> Cerrar sesión </router-link>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 sm:py-2 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
