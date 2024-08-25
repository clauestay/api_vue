<script setup>
    defineProps({
        error: Object
    })
</script>
<template>
    <section class="relative z-10 bg-morado-dark min-h-screen flex items-center justify-center">
        <div class="container mx-auto">
            <div class="flex justify-center">
                <div class="w-full px-4 text-center">
                    <div class="mx-auto max-w-[400px]">
                        <h4 class="mb-3 text-[22px] font-semibold leading-tight text-white">
                            Ha cerrado la sesión.
                        </h4>
                        <a href="http://intranet.incancer.cl"
                            class="inline-block rounded-lg border border-white px-8 py-3 text-center text-base font-semibold text-white transition hover:bg-white hover:text-primary">
                            Volver a intranet
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="absolute top-0 left-0 -z-10 flex h-full w-full items-center justify-between space-x-5 md:space-x-8 lg:space-x-14">
            <div class="h-full w-1/3 bg-gradient-to-t from-[#FFFFFF14] to-[#C4C4C400]"></div>
            <div class="flex h-full w-1/3">
                <div class="h-full w-1/2 bg-gradient-to-b from-[#FFFFFF14] to-[#C4C4C400]"></div>
                <div class="h-full w-1/2 bg-gradient-to-t from-[#FFFFFF14] to-[#C4C4C400]"></div>
            </div>
            <div class="h-full w-1/3 bg-gradient-to-b from-[#FFFFFF14] to-[#C4C4C400]"></div>
        </div>
    </section>
</template>