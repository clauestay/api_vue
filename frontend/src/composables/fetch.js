import { ref, watchEffect, toValue } from "vue";
import { alertaError } from "@/helpers/AlertasSweetAlert";
import { useAuthStore } from "@/stores/auth";

export function useFetch(url) {
  const authStore = useAuthStore();
  axios.defaults.headers.common["Authorization"] = "Bearer " + authStore.authToken;

  const data = ref([]);
  const error = ref(null);
  const loading = ref(false);

  const fetchData = async () => {
    loading.value = true;
    data.value = [];
    error.value = null;

    try {
      const response = await axios.get(toValue(url));
      data.value = response;
    } catch (err) {
      error.value = err;
      alertaError("Error al obtener el listado de los turnos.");
    } finally {
      loading.value = false;
    }
  };

  // const borrarRegistro = async (url) => {
  //   try {
  //     const response = await axios.delete(toValue(url));
  //     if(response.data.success) {
  //       alertaExito(response.data.success);
  //       await fetchData();
  //     } else if (response.data.error) {
  //       alertaError(response.data.error);
  //     }
  //   } catch (err) {
  //     manejarError(err, "Error al intentar borrar el turno.");
  //   }
  // };

  return { data, error, loading, fetchData/* borrarRegistro*/ };
}
