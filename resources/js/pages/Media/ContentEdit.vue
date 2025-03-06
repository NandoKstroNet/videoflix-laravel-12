<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';

import InputError from '@/components/InputError.vue';
import NavLink from '@/components/TextLink.vue';
import PrimaryButton from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import InputLabel from '@/components/ui/label/Label.vue';
import { Select } from '@/components/ui/select';
import TextArea from '@/components/ui/textarea/TextArea.vue';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';

onMounted(() => {
    Echo.channel('videos')
        .listen('.App\\Events\\VideoThumbCreated', (e) => {
            const video = getVideoById(e.videoId);
            console.log(e, video);
            if (!video) return;

            video.thumb = e.thumb;
        })
        .listen('.App\\Events\\VideoEncodingStarted', (e) => {
            const video = getVideoById(e.videoId);

            if (!video) return;

            video.encoding = true;
        })
        .listen('.App\\Events\\VideoEncodingProgress', (e) => {
            const video = getVideoById(e.videoId);

            if (!video) return;

            if (!video.encoding) video.encoding = true;

            video.encodingProgress = e.percentage;
        })
        .listen('.App\\Events\\VideoEncodingFinished', (e) => {
            const video = getVideoById(e.videoId);

            if (!video) return;

            video.encoding = false;
        });
});

defineProps({ content: {} });

const videos = reactive(usePage().props.content.videos);

const getVideoById = (id) => {
    return videos.find((video) => video.id == id);
};

const isDragged = ref(false);
const filesFront = ref([]);

const handleDragEve = (e) => {
    resetFilesProp();
    mainHandleImages(e.dataTransfer.files);
    isDragged.value = false;
};
const imagesHandle = (e) => {
    resetFilesProp();
    mainHandleImages(e.target.files);
};

const resetFilesProp = () => {
    form.photo = [];
    filesFront.value = [];
};

const mainHandleImages = (target) => {
    let images = target;

    form.photo = images.length ? images[0] : [];

    mountFilesFront(images);
};

const mountFilesFront = (images) => {
    Array.from(images).forEach((image) => {
        const reader = new FileReader(); //mdn.io/FileReader
        reader.readAsDataURL(image);

        reader.onload = (e) => {
            filesFront.value.push(e.target.result);
        };
    });
};

const form = useForm(usePage().props.content);

const opts = [
    { label: 'Filme', value: 1 },
    { label: 'Série', value: 2 },
];

const submit = () => {
    let data = {
        _token: usePage().props.csrf_token,
        _method: 'PUT',
        title: form.title,
        description: form.description,
        body: form.body,
        type: form.type,
        photo: form.photo,
    };

    router.post(route('media.contents.update', form.id), data);
};
</script>
<template>
    <Head title="Conteúdos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Atualizar Conteúdo</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto w-full sm:px-6 lg:px-8">
                <div class="flex w-full justify-end py-4">
                    <NavLink
                        :href="route('media.contents.videos.upload', content)"
                        class="rounded border border-green-900 bg-green-600 !py-3 px-6 !text-white transition duration-300 ease-in-out hover:bg-green-900"
                    >
                        Realizar Upload Vídeo(s)
                    </NavLink>
                </div>
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="mb-10 overflow-hidden shadow sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="px-4 sm:px-6 lg:px-8">
                                <form @submit.prevent="submit" novalidate>
                                    <div>
                                        <InputLabel for="title">Título</InputLabel>

                                        <Input id="title" type="text" v-model="form.title" required autofocus />

                                        <InputError class="mt-2" :message="form.errors.title" />
                                    </div>

                                    <div class="mt-4">
                                        <InputLabel for="description">Descrição</InputLabel>

                                        <Input
                                            id="description"
                                            type="text"
                                            class="mt-1 block w-full p-2"
                                            v-model="form.description"
                                            required
                                            autofocus
                                            autocomplete="username"
                                        />

                                        <InputError class="mt-2" :message="form.errors.description" />
                                    </div>

                                    <div class="mt-4">
                                        <InputLabel for="body">Conteúdo</InputLabel>

                                        <TextArea id="body" class="mt-1 block w-full p-2" v-model="form.body" />

                                        <InputError class="mt-2" :message="form.errors.body" />
                                    </div>

                                    <div class="mt-4">
                                        <InputLabel for="type" value="Tipo Conteúdo" />

                                        <Select id="type" :payloadOptions="opts" class="mt-1 block w-full p-2" v-model="form.type" />

                                        <InputError class="mt-2" :message="form.errors.type" />
                                    </div>

                                    <div class="mt-4">
                                        <div class="w-full py-10 text-left">
                                            <h2 class="font-bold">Foto de Capa</h2>
                                        </div>
                                        <div class="flex justify-around">
                                            <div class="flex w-[48%] items-center justify-center">
                                                <InputLabel
                                                    @dragover.prevent="isDragged = true"
                                                    @dragleave.prevent="isDragged = false"
                                                    @drop.prevent="handleDragEve"
                                                    for="photos"
                                                    class="flex h-28 w-full items-center justify-center rounded border-2 border-dashed border-white"
                                                    :class="{
                                                        'bg-gray-900': isDragged,
                                                    }"
                                                    >Clique ou arraste a foto da capa (1280x720)</InputLabel
                                                >

                                                <TextInput id="photos" type="file" class="sr-only" @change="imagesHandle" />

                                                <InputError class="mt-2" :message="form.errors.photo" />
                                            </div>

                                            <div
                                                class="mt-10 flex w-[48%] items-center justify-center border-l border-gray-900 px-10 pt-10"
                                                v-if="filesFront.length"
                                            >
                                                <div v-for="(img, key) of filesFront" :key="key">
                                                    <img :src="img" class="rounded bg-white p-1 shadow" />
                                                </div>
                                            </div>
                                            <div v-else class="mt-10 flex w-[48%] items-center justify-center border-l border-gray-900 px-10 pt-10">
                                                <img v-if="form.cover" :src="`/storage/${form.cover}`" class="rounded bg-white p-1 shadow" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-8">
                                        <PrimaryButton
                                            :class="{
                                                'opacity-25': form.processing,
                                            }"
                                            :disabled="form.processing"
                                        >
                                            Atualizar
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>

                            <!-- Componentizar trecho abaixo-->
                            <div v-if="videos.length" class="mt-10 grid grid-cols-4 gap-3 border-t border-gray-500 p-7 pt-10">
                                <div class="w-[230px]" v-for="video of videos" :key="video.id">
                                    <img
                                        v-if="video.thumb"
                                        :src="`/storage/${video.thumb}`"
                                        :alt="`Capa vídeo ${video.name}`"
                                        class="mb-4 rounded bg-white p-1 shadow-xl"
                                    />
                                    <img
                                        v-if="!video.thumb"
                                        :src="`/storage/no-photo.jpg`"
                                        :alt="`Capa vídeo ${video.name}`"
                                        class="mb-4 rounded bg-white p-1 shadow-xl"
                                    />
                                    <h2 class="font-white mb-4 overflow-hidden text-clip text-xl font-bold">
                                        {{ video.name }}
                                    </h2>

                                    <div class="space-y-1" v-if="video.encoding">
                                        <div class="h-3 overflow-hidden rounded bg-gray-100 shadow-inner">
                                            <div
                                                class="h-full bg-green-500"
                                                v-bind:style="{
                                                    width: `${video.encodingProgress}%`,
                                                }"
                                            ></div>
                                        </div>
                                        <div class="text-sm font-bold text-white">Convertendo vídeo</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fim trecho Componentizar -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
