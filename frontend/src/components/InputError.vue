<script setup>
defineProps({
    message: {
        type: String,
    },
    textColorError: {
        type: String,
        default: 'text-red-600',
    }
});
</script>

<template>
    <div v-show="message">
        <p :class="`text-sm ${textColorError}`">
            {{ message }}
        </p>
    </div>
</template>
