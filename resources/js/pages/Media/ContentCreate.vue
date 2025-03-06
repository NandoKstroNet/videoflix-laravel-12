<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import InputError from '@/components/InputError.vue';
import PrimaryButton from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import InputLabel from '@/components/ui/label/Label.vue';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import TextArea from '@/components/ui/textarea/TextArea.vue';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';

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
    form.cover = [];
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

const form = useForm({
    title: null,
    description: null,
    body: null,
    photo: null,
    type: null,
});
const opts = [
    { label: 'Filme', value: 1 },
    { label: 'Série', value: 2 },
];

const submit = () => {
    console.log(form);

    form.post(route('media.contents.store'));
};
</script>
<template>
    <Head title="Conteúdos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Criar Conteúdo</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto w-full sm:px-6 lg:px-8">
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
                                        <InputLabel for="type">Tipo Conteúdo</InputLabel>

                                        <Select id="type" :payloadOptions="opts" v-model="form.type">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Seleciona um tipo do conteúdo" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="o of opts" :key="o.value" :value="o.value">
                                                    {{ o.label }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>

                                        <InputError class="mt-2" :message="form.errors.type" />
                                    </div>

                                    <!-- Input File & Exibicao Foto-->
                                    <div class="mt-4">
                                        <div class="w-full py-10 text-left">
                                            <h2 class="font-bold">Foto de Capa</h2>
                                        </div>
                                        <div class="flex justify-around">
                                            <div
                                                class="flex items-center justify-center"
                                                :class="{
                                                    'w-[48%]': filesFront.length,
                                                    'w-full': !filesFront.length,
                                                }"
                                            >
                                                <InputLabel
                                                    @dragover.prevent="isDragged = true"
                                                    @dragleave.prevent="isDragged = false"
                                                    @drop.prevent="handleDragEve"
                                                    for="photos"
                                                    class="bg-black-700 flex h-28 w-full items-center justify-center rounded border-2 border-dashed border-white"
                                                    :class="{
                                                        'bg-gray-900': isDragged,
                                                    }"
                                                    >Clique ou arraste a foto da capa (1280x720)</InputLabel
                                                >

                                                <Input id="photos" type="file" class="sr-only" @change="imagesHandle" />

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
                                        </div>
                                    </div>
                                    <!-- Input File & Exibicao Foto-->

                                    <div class="mt-8">
                                        <PrimaryButton
                                            :class="{
                                                'opacity-25': form.processing,
                                            }"
                                            :disabled="form.processing"
                                        >
                                            Criar
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
