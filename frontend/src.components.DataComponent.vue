<template>
    <div>
      <ul>
        <li v-for="item in data" :key="item.id">{{ item.name }}</li>
      </ul>
    </div>
  </template>

  <script>
  import api from '@/services/api';

  export default {
    data() {
      return {
        data: []
      }
    },
    async mounted() {
      try {
        const response = await api.get('/data');
        this.data = response.data;
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    }
  }
  </script>