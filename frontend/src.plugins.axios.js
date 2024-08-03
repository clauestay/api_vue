import axios from 'axios'

export const axiosInstance = axios.create({
  baseURL: process.env.VUE_APP_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  withCredentials: true
})

export default {
  install: (app) => {
    app.config.globalProperties.$axios = axiosInstance
  }
}

export const useAxios = () => {
  return { axios: axiosInstance }
}