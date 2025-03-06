<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AppLayout.vue';

import PlayerFull from '@/Components/VideoFlix/Players/PlayerFull.vue';
import PlayerList from '@/Components/VideoFlix/Players/PlayerList.vue';
import { type BreadcrumbItem } from '@/types';

defineProps({ content: {}, videos: {} });

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Meu Conteúdo',
        href: '/my-content',
    },
    {
        title: 'Assista',
        href: '',
    },
];
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <div class="w-full">
            <PlayerList v-if="content.type === 2" :videos="videos" />

            <PlayerFull v-if="content.type === 1" />
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
