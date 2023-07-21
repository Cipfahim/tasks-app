<script setup>
import {Link} from "@inertiajs/vue3";

defineProps({
    links: Array,
})
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap mt-2 justify-center">
            <template v-for="(link) in links">
                <div v-if="link.url === null"
                     class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded dark-mode-text"
                     v-html="link.label"/>
                <Link
                    v-else
                    class="mr-1  mb-1 px-4 py-3 text-sm leading-4 border rounded !dark-bg-primary dark-mode-text"
                    :class="{ 'bg-white border-primary': link.active }" :href="link.url">
                    <span v-html="link.label"></span>
                </Link>
            </template>
        </div>
    </div>
</template>

