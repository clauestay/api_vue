<template>
  <div>
    <ul>
      <li v-for="item in data" :key="item.id">{{ item.name }}</li>
    </ul>
  </div>
</template>

<script>
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