import 'sweetalert2/dist/sweetalert2.min.css';
// import { nextTick } from 'vue/runtime-core';
import { useAuthStore } from '@/stores/auth';
import { alertaExito } from './helpers/AlertasSweetAlert';

export async function sendRequest(method, params, url, redirect=''){
    const authStore = useAuthStore();

    axios.defaults.headers.common['Authorization'] = 'Bearer '+authStore.authToken;

    let res;
    await axios({ method: method, url: url, params: params }).then(
        response => {
            res = response.data.status,
            alertaExito(response.data.message);
            setTimeout(() => (redirect !== '') ? window.location.href = redirect : '', 2000)
        }).catch((errors) => {
            let desc='';
            res = errors.response.data.status;
            errors.response.data.errors.map(
                (e) => (desc = desc + ' '+e)
            )
            alertaError(desc);
        })
    return res;
}