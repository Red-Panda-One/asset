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
import SearchMultiselect from '@/Components/SearchMultiselect.vue';

// Define props before the interface
defineProps({
    availableFiles: {
        type: Object,
        required: true
    }
});

interface KitForm {
    name: string;
    description: string;
    custom_id: string;
    image: File | null;
    additional_files: File[];
    selected_files: number[]; // Add this line
    status: Status;
}

const form = useForm<KitForm>({
    name: '',
    description: '',
    custom_id: '',
    image: null,
    additional_files: [],
    selected_files: [], // Add this line
    status: 'Available'
});

const imagePreview = ref(null);
const fileName = ref('');

// Remove the console.log(availableFiles) line as it's no longer needed

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

const handleAdditionalFiles = (e: Event) => {
    if (!(e.target instanceof HTMLInputElement) || !e.target.files) return;
    const files = Array.from(e.target.files);
    for (const file of files) {
        if (file.size > 4 * 1024 * 1024) {
            alert(`File ${file.name} exceeds 4MB limit`);
            continue;
        }
        if (!['image/jpeg', 'image/png', 'application/pdf'].includes(file.type)) {
            alert(`File ${file.name} is not a supported format`);
            continue;
        }
        form.additional_files.push(file);
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
                    <InputLabel for="custom_id" value="Custom ID" />
                    <TextInput
                        id="custom_id"
                        v-model="form.custom_id"
                        type="text"
                        class="block mt-1 w-full"
                    />
                    <InputError :message="form.errors.custom_id" class="mt-2" />
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

                <!-- Add this before the submit button -->
                <div>
                    <InputLabel for="additional_files" value="Additional Files" />
                    <div class="mt-1">
                        <input
                            type="file"
                            multiple
                            @change="handleAdditionalFiles"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100"
                        />
                        <p class="mt-1 text-xs text-gray-500">Upload multiple files (PNG, JPG, PDF up to 4MB each)</p>
                    </div>
                    <div v-if="form.additional_files.length > 0" class="mt-3 space-y-2">
                        <div v-for="(file, index) in form.additional_files" :key="index" class="flex items-center p-2 bg-gray-50 rounded-md">
                            <div class="ml-2">
                                <p class="text-sm text-gray-700">{{ file.name }}</p>
                                <p class="text-xs text-gray-500">{{ (file.size / 1024 / 1024).toFixed(2) }} MB</p>
                            </div>
                        </div>
                    </div>
                    <InputError :message="form.errors.additional_files" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="additional_files" value="Link Existing Files" />
                    <div class="relative">
                        <SearchMultiselect
                            id="additional_files"
                            v-model="form.selected_files"
                            :display="false"
                            :options="availableFiles.data"
                            label="name"
                            value-prop="id"
                            :multiple="true"
                            placeholder="Search and select files"
                            class="mt-1"
                        />
                    </div>
                    <InputError :message="form.errors.selected_files" class="mt-2" />
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
