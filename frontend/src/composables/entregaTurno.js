import { ref, watchEffect, toValue } from "vue";
import { useAuthStore } from "@/stores/auth";
import { manejarError } from '@/functions';


export function useEntregaTurno(url) {

    const authStore = useAuthStore();
    axios.defaults.headers.common["Authorization"] = "Bearer " + authStore.authToken;

    const medicos = ref([]);
    const unidades = ref([]);
    // const medico_entrega = ref([]);

    const getMedicos = async () => {
        try {
            const response = await axios.get(toValue(url));
            medicos.value = response.data.medicos;
        } catch (err) {
            manejarError(err, "Error al obtener el listado de los medicos.");
        }
    }

    const getUnidades = async () => {
        try {
            const response = await axios.get(toValue(url));
            unidades.value = response.data.unidades;
        } catch (err) {
            manejarError(err, "Error al obtener el listado de las unidades.");
        }
    }

    // const getMedicoEntrega = async () => {
    //     try {
    //         const response = await axios.get(toValue(url));
    //         medico_entrega.value = response.data.medico_entrega;
    //         console.log(medico_entrega.value);
    //     } catch (err) {
    //         console.log(err);
    //         manejarError(err, "Error al obtener al médico que entrega el turno.");
    //     }
    // }

    watchEffect(() => {
        getMedicos();
    });

    watchEffect(() => {
        getUnidades();
    });

    // watchEffect(() => {
    //     getMedicoEntrega();
    // });

    return {
        medicos,
        unidades,
        // medico_entrega
    }
}