<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

interface KitData {
    id: string;
    name: string;
    description: string;
    image?: string;
}

interface KitProps {
    kit: {
        data: KitData;
    };
}

const props = defineProps<KitProps>();

// Add default values and error handling
const kitData = props.kit.data || { name: '', description: '', image: null };

console.log(props.kit);


interface KitForm {
    name: string;
    description: string;
    image: File | null;
}

const form = useForm<KitForm>({
    name: kitData.name,
    description: kitData.description,
    image: null
});

const imagePreview = ref(kitData.image ? `/storage/${kitData.image}` : null);
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
            if (e.target) {
                imagePreview.value = e.target.result as string;
            }
        };
        reader.readAsDataURL(file);
    }
};

const handleDrop = (e: DragEvent) => {
    e.preventDefault();
    if (!e.dataTransfer) return;
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
            if (e.target) {
                imagePreview.value = e.target.result as string;
            }
        };
        reader.readAsDataURL(file);
    } else {
        alert('Please upload PNG or JPG/JPEG files only');
    }
};

const submit = () => {
    form.post(route('kits.update', props.kit.data.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
            window.scrollTo(0, 0);
        },
    });
};
</script>

<template>
    <AppLayout title="Edit Kit">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Kit
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
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="description" value="Description" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                        rows="4"
                    ></textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>

                <div>
                    <InputLabel value="Image" />
                    <div
                        @drop.prevent="handleDrop"
                        @dragover.prevent
                        class="flex justify-center px-6 pt-5 pb-6 mt-1 rounded-md border-2 border-gray-300 border-dashed dark:border-gray-600"
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
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label
                                    for="image-upload"
                                    class="relative font-medium text-orange-600 bg-white rounded-md cursor-pointer dark:bg-gray-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-orange-500 focus-within:ring-offset-2 hover:text-orange-500"
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
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG up to 4MB</p>
                        </div>
                    </div>
                    <div v-if="imagePreview" class="flex items-center p-4 mt-3 bg-gray-50 rounded-md dark:bg-gray-800">
                        <img :src="imagePreview" class="object-cover w-16 h-16 rounded" />
                        <div class="ml-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ form.image ? form.image.name : fileName || 'Current image' }}</p>
                        </div>
                    </div>
                    <InputError :message="form.errors.image" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update Kit
                    </PrimaryButton>
                </div>
            </form>
        </Container>
    </AppLayout>
</template>
