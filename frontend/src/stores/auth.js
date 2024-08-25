import { defineStore } from 'pinia';
import axios from 'axios';
import { alertaError, alertaExito } from '@/helpers/AlertasSweetAlert';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        authUser: null,
        authToken: null,
        allowedLoginUrls: ['entrega-turno'] }), // aca va el listado de urls de los nuevos módulos que usen vue y la api.
    getters: {
        user:(state) => state.authUser,
        token:(state) => state.authToken
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async login(url, login, password) {
            if (!this.allowedLoginUrls.includes(url)) {
                alertaError('La URL de login no está permitida.');
                return;
            }

            await axios.post('/login', {
                    login: login,
                    password: password
                }).then((response) => {
                this.authUser = response.data.user;
                this.authToken = response.data.token;
                alertaExito('Usuario autenticado con exito!');
                setTimeout(() => {
                    this.router.push(`/${url}/inicio`);
                })
            }).catch((error) => {
                let message = '';
                error.response.data.errors.map(
                    (e) => (message = message + ' '+e)
                )
                console.log(message);
                alertaError(message);
            });
        },
        async logout() {
            axios.defaults.headers.common["Authorization"] = "Bearer " + this.authToken;
            await axios.post('logout').finally(() => {
                this.authUser = null;
                this.authToken = null;
                localStorage.removeItem('auth');
                axios.defaults.headers.common["Authorization"] = null;
                this.router.push({ name: 'Logout' });
            }).catch((error) => {
                console.log(error);
                alertaError(error);
            });
        }
    },
    persist: {
        storage: localStorage,
      },
});
