<script setup>
const props = defineProps({
    cantidad: {
        type: Object,
        default: () => ({}),
    }
});
</script>
<template>
    <div>
        <svg class="h-12 w-12 text-[#0080bc] fill-current group-hover:animate-bounce cursor-pointer"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 68 64">
            <g transform="translate(-1287 -423)">
                <rect class="text-[#0080bc]" width="68" height="64" rx="9" transform="translate(1287 423)" />
                <g transform="translate(-1333.995 28.736)">
                    <path class="text-[#fdfffe]"
                        d="M2691.8,429.944c.507-.485.956-.914,1.4-1.344,1.976-1.9,3.943-3.806,5.93-5.694a1.952,1.952,0,0,1,3.259.881,1.929,1.929,0,0,1-.589,2q-2.9,2.788-5.8,5.573-2.417,2.324-4.833,4.65a1.958,1.958,0,0,1-2.762,0q-5.118-5.157-10.228-10.322a1.956,1.956,0,0,1-.574-2.039,1.835,1.835,0,0,1,1.416-1.348,1.977,1.977,0,0,1,1.962.619q2.971,3.02,5.959,6.025c.277.28.545.57.889.931.013-.219.025-.33.026-.441q.085-10.819.165-21.638a1.953,1.953,0,0,1,1.09-1.887,1.856,1.856,0,0,1,2.042.175,1.947,1.947,0,0,1,.8,1.688q-.041,4.976-.076,9.953-.041,5.535-.084,11.07C2691.795,429.147,2691.8,429.495,2691.8,429.944Z"
                        transform="translate(-35.057)" />
                    <rect class="text-[#fdfffe]" width="35.99" height="4.044" rx="2.022" transform="translate(2637 442.798)" />
                </g>
            </g>
        </svg>
        <div v-if="props.cantidad" class="inline-flex absolute -top-2 -right-2 justify-center items-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full border-2 border-white dark:border-gray-900 group-hover:animate-bounce">{{ props.cantidad }}</div>
    </div>
</template>