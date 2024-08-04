<template>
  <div>
    a
    {{ items }}
  </div>
</template>

<script setup>
console.log("datacomponent");
import { ref, onMounted, inject } from 'vue'
const axios = inject('axios');
const items = ref([])

const fetchData = async () => {
  if (axios) {
    try {
      const response = await axios.get('/example');
      console.log(response);
      items.value = response.data.message;
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  } else {
    console.error('Axios no está inyectado correctamente.');
  }
};

// Función para obtener los parámetros de la URL
function getQueryParams() {
    const params = new URLSearchParams(window.location.search);
    return {
        login: params.get('login'),
        password: params.get('password'),
    };
}

// Capturar los parámetros de la URL
const { login, password } = getQueryParams();

if (login && password) {
    // Hacer la llamada a la API de Laravel
    axios.post('http://localhost:80/api/login', {
        login: login,
        password: password
    })
    .then(response => {
        console.log('Login exitoso:', response.data);
        // Manejar el login exitoso
        const token = response.data.token; // Asume que el token está en response.data.token
        localStorage.setItem('authToken', token);
    })
    .catch(error => {
        console.error('Error en el login:', error);
        // Manejar el error en el login
    });
}

// onMounted(fetchData);
</script>