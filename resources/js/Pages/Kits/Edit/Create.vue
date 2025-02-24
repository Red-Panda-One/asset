<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';
import StatusSelector from '@/Components/StatusSelector.vue';
import type { Status } from '@/types/status';

interface KitForm {
    name: string;
    description: string;
    image: File | null;
    status: Status;
}

const form = useForm<KitForm>({
    name: '',
    description: '',
    image: null,
    status: 'Available'
});

const imagePreview = ref(null);
const fileName = ref('');

const handleImageUpload = (e: Event) => {
    if (!(e.target instanceof HTMLInputElement) || !e.target.files) return;
    const file = e.target.files[0];
    if (file) {
        if (file.size > 4 * 1024 * 1024) {
            alert('Image size should not exceed 4MB');
            return;
        }
        form.image = file;
        fileName.value = file.name;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const handleDrop = (e: DragEvent) => {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file && (file.type === 'image/png' || file.type === 'image/jpeg')) {
        if (file.size > 4 * 1024 * 1024) {
            alert('Image size should not exceed 4MB');
            return;
        }
        form.image = file;
        fileName.value = file.name;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        alert('Please upload PNG or JPG/JPEG files only');
    }
};

const submit = () => {
    form.post(route('kits.store'), {
        preserveState: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
            window.scrollTo(0, 0);
        },
    });
};
</script>

<template>
    <AppLayout title="Add Kit">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Add Kit
            </h2>
        </template>
        <Container>
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="block mt-1 w-full"
                        required
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="description" value="Description" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        rows="4"
                    ></textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>

                <div>
                    <InputLabel value="Image" />
                    <div
                        @drop.prevent="handleDrop"
                        @dragover.prevent
                        class="flex justify-center px-6 pt-5 pb-6 mt-1 rounded-md border-2 border-gray-300 border-dashed"
                    >
                        <div class="space-y-1 text-center">
                            <svg
                                class="mx-auto w-12 h-12 text-gray-400"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 48 48"
                            >
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label
                                    for="image-upload"
                                    class="relative font-medium text-orange-600 bg-white rounded-md cursor-pointer focus-within:outline-none focus-within:ring-2 focus-within:ring-orange-500 focus-within:ring-offset-2 hover:text-orange-500"
                                >
                                    <span>Upload a file</span>
                                    <input
                                        id="image-upload"
                                        type="file"
                                        class="sr-only"
                                        accept="image/png,image/jpeg"
                                        @change="handleImageUpload"
                                    />
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG up to 4MB</p>
                        </div>
                    </div>

                    <div v-if="imagePreview" class="flex items-center p-4 mt-3 bg-gray-50 rounded-md">
                        <img :src="imagePreview" class="object-cover w-16 h-16 rounded" />
                        <div class="ml-4">
                            <p class="text-sm text-gray-700">{{ fileName }}</p>
                        </div>
                    </div>
                    <InputError :message="form.errors.image" class="mt-2" />

                </div>

                <div>
                        <StatusSelector
                            v-model="form.status"
                            :error="form.errors.status"
                        />
                </div>

                <div class="flex justify-end">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create Kit
                    </PrimaryButton>
                </div>
            </form>
        </Container>
    </AppLayout>
</template>
