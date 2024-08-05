<template>
    <div class="flex">
      <div
        class="w-full m-auto shadow-lg shadow-pink-400 border-4 border-t-purple-600 border-r-pink-600 border-b-pink-600 border-l-indigo-600 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"
      >
        <p class="font-bold text-white text-2xl">
          {{ currentTime.toLocaleTimeString() }}
        </p>
        <p class="font-bold text-white text-sm flex justify-end mr-3">
          {{ currentTime.toLocaleDateString() }}
        </p>
      </div>
    </div>
  </template>

  <script setup>
  import { useCurrentTime } from '../helpers/UseCurrentTime.js';
  const { currentTime } = useCurrentTime();

  defineProps({
    currentTime: {
      type: Date,
      default: () => new Date(),
    }
  });
</script>