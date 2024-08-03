import { ref } from 'vue'
import { useAxios } from '../plugins/axios'

export function useDataFetching() {
  const { axios } = useAxios()
  const data = ref([])
  const error = ref(null)
  const loading = ref(false)

  const fetchData = async (endpoint) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.get(endpoint)
      data.value = response.data
    } catch (err) {
      error.value = err.message || 'An error occurred'
    } finally {
      loading.value = false
    }
  }

  return {
    data,
    error,
    loading,
    fetchData
  }
}