import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('authToken') || '',
    }),
    actions: {
        async login(username, password) {
            try {
                const response = await axios.post('http://localhost:80/api/login', {
                    username: username,
                    password: password
                });
                this.token = response.data.token;
                localStorage.setItem('authToken', this.token);
                // Redirigir a la página principal de la aplicación o a la ruta deseada
                window.location.href = '/home';
            } catch (error) {
                console.error('Error en el login:', error);
            }
        },
        logout() {
            this.token = '';
            localStorage.removeItem('authToken');
            window.location.href = '/login';
        }
    }
});
