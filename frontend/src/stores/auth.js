import { defineStore } from 'pinia';
import axios from 'axios';
import { alertaError, alertaExito } from '@/helpers/AlertasSweetAlert';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        authUser: null, authToken: null }),
    getters: {
        user:(state) => state.authUser,
        token:(state) => state.authToken
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async login(login, password) {
            // await axios.getToken();
            await axios.post('http://localhost:80/api/login', {
                    login: login,
                    password: password
                }).then((response) => {
                this.authUser = response.data.user;
                this.authToken = response.data.token;
                alertaExito('Usuario autenticado con exito!');
                setTimeout(() => {
                    this.router.push({ name: 'Inicio' });
                })
            }).catch((error) => {
                let message = '';
                error.response.data.errors.map(
                    (e) => (message = message + ' '+e)
                )
                console.log(message);
                alertaError(message);
                // this.router.push({ name: 'Error' });
            });
        },
        async logout() {
            await axios.post('http://localhost:80/api/logout', this.authToken);
            this.authUser = null;
            this.authToken = null;
            // delete storage.token
            localStorage.removeItem('auth');
            this.router.push('/login');
        }
    },
    // presist: true
    persist: {
        storage: localStorage,
      },
});
