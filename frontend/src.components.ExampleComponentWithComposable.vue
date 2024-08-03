<template>
    <div>
      <div v-if="loading">Loading...</div>
      <div v-else-if="error">{{ error }}</div>
      <ul v-else>
        <li v-for="item in data" :key="item.id">{{ item.name }}</li>
      </ul>
    </div>
  </template>

  <script setup>
  import { onMounted } from 'vue'
  import { useDataFetching } from '../composables/useDataFetching'

  const { data, error, loading, fetchData } = useDataFetching()

  onMounted(() => {
    fetchData('/api/data')
  })
  </script>