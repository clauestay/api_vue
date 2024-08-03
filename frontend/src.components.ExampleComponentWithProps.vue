<template>
    <div>
      <h2>{{ title }}</h2>
      <ul>
        <li v-for="item in data" :key="item.id">{{ item.name }}</li>
      </ul>
    </div>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue'
  import { useAxios } from '../plugins/axios'

  const props = defineProps({
    title: {
      type: String,
      default: 'Data List'
    }
  })

  const { axios } = useAxios()
  const data = ref([])

  const fetchData = async () => {
    try {
      const response = await axios.get('/api/data')
      data.value = response.data
    } catch (error) {
      console.error('Error fetching data:', error)
    }
  }

  onMounted(fetchData)
  </script>