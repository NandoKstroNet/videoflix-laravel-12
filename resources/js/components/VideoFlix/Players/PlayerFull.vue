<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import Hls from 'hls.js';
import { onMounted, ref } from 'vue';

defineProps({ videos: {} });

const video = ref(usePage().props.videos[0]);
const playerEl = ref();
let player = {};
console.log(video.value);
onMounted(() => {
    player = new Hls();
    player.loadSource(
        route('stream_player', {
            code: video.value.code,
            video: video.value.video,
        }),
    );
    player.attachMedia(playerEl.value);
});
</script>
<template>
    <Head :title="`Assistindo: ${video.name}`" />
    <div class="mx-auto max-w-7xl py-10">
        <div class="flex max-w-full justify-between">
            <video ref="playerEl" id="player" class="ml-10 mt-8 h-[780px] max-w-full" controls></video>
        </div>
    </div>
</template>
