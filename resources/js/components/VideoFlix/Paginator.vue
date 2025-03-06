<script setup>
import NavLink from '@/Components/NavMain.vue';

defineProps({
    meta: {},
});
</script>

<template>
    <div id="pagination" class="flex justify-end border-t border-gray-200 bg-gray-50 py-2 pr-10">
        <NavLink
            :href="link.url"
            v-for="link in meta.links"
            v-show="link.url"
            :key="link.label"
            class="ml-4 rounded border border-gray-300 !px-4 !py-2 transition duration-300 ease-in-out hover:border-gray-600 hover:bg-gray-900 hover:text-white"
            :class="{
                'border-gray-600 bg-gray-900 text-white': link.active,
            }"
            >{{ link.label }}</NavLink
        >
    </div>
</template>

<style scoped></style>
