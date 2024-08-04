import { defineStore } from 'pinia';
import axios from 'axios';

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
        async login(form){
            await axios.getToken();
            await axios.post('/login', form).then((response) => {
                this.authUser = response.data.user;
                this.authToken = response.data.token;
                this.router.push({ name: 'inicio' });
                // localStorage.setItem('authUser', JSON.stringify(this.authUser));
                // localStorage.setItem('authToken', this.authToken);
            }).catch((error) => {
                console.log(error);
                    let message = '';
                    error.response.data.errors.map(
                        (e) => (message = message + ' '+e)
                    )
                    console.log(message);
            });
        },
        async logout() {
            await axios.post('/logout', this.authToken);
            this.authUser = null;
            this.authToken = null;
            this.router.push('/login');
            // localStorage.removeItem('authToken');
            // window.location.href = '/login';
        }
    },
    presist: true
});
