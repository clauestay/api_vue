<template>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

// Función para obtener los parámetros de la URL
function getQueryParams() {
    const params = new URLSearchParams(window.location.search);
    return {
        url: params.get('url'),
        login: params.get('login'),
        password: params.get('password'),
    };
}

// Capturar los parámetros de la URL
const { url, login, password } = getQueryParams();

if (login && password) {
    authStore.login(url,login,password);
}
</script>