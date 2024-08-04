<script setup>
    const props = defineProps({
        pdfPath: {
            type: String,
            default: () => ({})
        }
    })

    // console.log(pdfPath);

    const mostrarManual = () => {
        const url = `${window.location.origin}/${props.pdfPath}`;
        window.open(url, '_blank');
    }
</script>

<template>
    <div class="float-right">
        <PrimaryButton class="bg-morado-dark hover:bg-morado-base focus:ring-4 font-medium rounded-lg text-sm px-5 text-white py-2.5 text-center mr-2 mb-2 cursor-pointer"
            @click="mostrarManual()">Manual PDF
        </PrimaryButton>
    </div>
</template>