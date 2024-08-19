import { computed } from 'vue';

const minDate = computed(() => {
    const fecha = new Date();
    fecha.setDate(fecha.getDate() - 1);

    const year = fecha.getFullYear();
    const month = String(fecha.getMonth() + 1).toString().padStart(2, '0');
    const day = String(fecha.getDate()).padStart(2, '0');
    const hours = String(fecha.getHours()).padStart(2, '0');
    const minutes = String(fecha.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day} 00:00:00`;
    // return `${year}-${month}-${day}T${hours}:${minutes}`;
    // return `${year}-${month}-${day} 00:00:00`;
  //   fecha.setDate(fecha.getDate() - 1);
  //   return fecha.toISOString().slice(0, 10); // Return only date part (YYYY-MM-DD)
  });

  const maxDate = computed(() => {
    const fecha = new Date();
    fecha.setDate(fecha.getDate() + 1);

    const year = fecha.getFullYear();
    const month = String(fecha.getMonth() + 1).toString().padStart(2, '0');
    const day = String(fecha.getDate()).padStart(2, '0');
    const hours = String(fecha.getHours()).padStart(2, '0');
    const minutes = String(fecha.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day} 23:59:59`;
    // return `${year}-${month}-${day}T${hours}:${minutes}`;
  //   fecha.setDate(fecha.getDate() + 1);
  //   return fecha.toISOString().slice(0, 10); // Return only date part (YYYY-MM-DD)
  });

  export {
    minDate,
    maxDate
}