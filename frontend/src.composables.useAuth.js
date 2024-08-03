import { ref } from 'vue'
import { useAxios } from '../plugins/axios'

export function useAuth() {
  const { axios } = useAxios()
  const user = ref(null)
  const error = ref(null)

  const login = async (email, password) => {
    try {
      await axios.get('/sanctum/csrf-cookie')
      const response = await axios.post('/login', { email, password })
      user.value = response.data.user
    } catch (err) {
      error.value = err.response.data.message
    }
  }

  const logout = async () => {
    try {
      await axios.post('/logout')
      user.value = null
    } catch (err) {
      error.value = err.response.data.message
    }
  }

  const checkAuth = async () => {
    try {
      const response = await axios.get('/api/user')
      user.value = response.data
    } catch (err) {
      user.value = null
    }
  }

  return {
    user,
    error,
    login,
    logout,
    checkAuth
  }
}