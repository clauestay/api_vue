import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost/api', // Ajusta esto según tu URL de producción
//   baseURL: process.env.VUE_APP_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

export default api;