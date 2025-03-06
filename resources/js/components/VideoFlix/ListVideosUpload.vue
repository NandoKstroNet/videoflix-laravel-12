<script setup>
import InputError from '@/components/InputError.vue';
import PrimaryButton from '@/components/ui/button/Button.vue';
import TextInput from '@/components/ui/input/Input.vue';
import InputLabel from '@/components/ui/label/Label.vue';
import Textarea from '@/components/ui/textarea/TextArea.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    upload: Object,
    content: Number,
});
const form = useForm({
    name: props.upload.name,
    description: '',
});

const emit = defineEmits();
</script>
<template>
    <div class="w-full overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex space-x-6">
                <div class="w-full max-w-[350px] space-y-3">
                    <div class="h-[180px] w-full" v-if="upload.thumb">
                        <img :src="`/storage/${upload.thumb}`" alt="" class="h-[180px] w-full rounded bg-white p-1 shadow" />
                    </div>
                    <div class="space-y-1" v-if="upload.encoding">
                        <div class="h-3 overflow-hidden rounded bg-gray-100 shadow-inner">
                            <div
                                class="h-full bg-green-500"
                                :style="{
                                    width: `${upload.encodingProgress}%`,
                                }"
                            ></div>
                        </div>
                        <div class="text-sm font-bold text-white">Convertendo vídeo</div>
                    </div>

                    <div class="space-y-1" v-if="upload.uploading">
                        <div class="h-3 overflow-hidden rounded bg-gray-100 shadow-inner">
                            <div
                                class="h-full bg-blue-500"
                                :style="{
                                    width: `${upload.uploadProgress}%`,
                                }"
                            ></div>
                        </div>
                        <div class="text-sm font-bold text-white">Enviando Vídeo</div>
                    </div>

                    <div class="flex items-center space-x-3" v-if="upload.uploading">
                        <button class="text-sm font-medium text-blue-500" @click="emit('pause', upload.id)" v-if="!upload.file.paused">Pausar</button>
                        <button class="text-sm font-medium text-blue-500" @click="emit('resume', upload.id)" v-if="upload.file.paused">
                            Continuar
                        </button>

                        <button class="text-sm font-medium text-blue-500" @click="emit('cancel', upload.id)">Cancelar envio</button>
                    </div>
                </div>

                <form
                    class="flex-grow space-y-6"
                    v-on:submit.prevent="
                        form.patch(
                            route('media.contents.videos.update', {
                                content: content,
                                video: upload.id,
                            }),
                            { preserveScroll: true, preserveState: true },
                        )
                    "
                >
                    <div>
                        <InputLabel for="name">Video Titulo</InputLabel>
                        <TextInput id="name" type="text" v-model="form.name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="description">Video Descrição</InputLabel>
                        <Textarea id="description" v-model="form.description" />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <div class="flex items-center space-x-3">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Atualizar </PrimaryButton>
                        <div class="text-sm font-bold text-white" v-if="form.recentlySuccessful">Video Atualizado</div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
