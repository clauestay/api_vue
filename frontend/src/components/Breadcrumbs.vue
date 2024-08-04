<template>
    <nav aria-label="Breadcrumb" v-if="breadcrumbs">
        <ol class="px-5 py-3 rounded flex flex-wrap bg-[#F7F7F7] text-sm">
            <li class="mr-4" v-for="page in breadcrumbs">
                <div class="flex items-center">
                    <span v-if="page ==='/'">/</span>
                    <a
                        v-else
                        :href="page.url"
                        :class="{ 'text-gray-500 hover:text-gray-800': page.current }"
                    >{{ page.title }}</a>
                </div>
            </li>
        </ol>
    </nav>
</template>

<script>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export default {
    setup() {
        // Insert an element between all elements, insertBetween([1, 2, 3], '/') results in [1, '/', 2, '/', 3]
        const insertBetween = (items, insertion) => {
            return items.flatMap(
                (value, index, array) =>
                    array.length - 1 !== index
                        ? [value, insertion]
                        : value,
            )
        }

        const breadcrumbs = computed(() => insertBetween(usePage().props.breadcrumbs || [], '/'))

        return {
            breadcrumbs,
        }
    },
}
</script>