<template>
    <div v-if="links?.length" style="margin-top: 16px; display: flex; gap: 6px; flex-wrap: wrap;">
        <button
            v-for="link in links"
            :key = "link.label"
            :disabled="!link.url"
            @click="go(link.url)"
            v-html="link.label"
            style="padding: 6px 10px;"
        >

        </button>

    </div>
</template>
    <script setup>
    import { router } from '@inertiajs/vue3'

    defineProps({
        links: Array,  
    })

    function go(url) {
        if (!url) return
            router.visit(url, {preserveState: true, preserveScroll: true})        
    }



    </script>
