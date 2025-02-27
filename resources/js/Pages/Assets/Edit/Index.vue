<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import { useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SearchMultiselect from '@/Components/SearchMultiselect.vue';
import { ref } from 'vue';
import StatusSelector from '@/Components/StatusSelector.vue';
import type { Status } from '@/types/status';

const props = defineProps({
    asset: {
        type: Object,
        required: true
    },
    categories: Object,
    locations: Object,
    tags: Object,
    selectedTags: Array,
});

console.log(props.asset);

const existingFiles = ref(props.asset.data.additional_files || []);

const removeExistingFile = (fileId) => {
    existingFiles.value = existingFiles.value.filter(file => file.id !== fileId);
    form.remove_files.push(fileId);
};

const removeNewFile = (index) => {
    form.additional_files.splice(index, 1);
};

const form = useForm({
    name: props.asset.data.name,
    description: props.asset.data.description,
    value: props.asset.data.value,
    category_id: props.asset.data.category_id,
    location_id: props.asset.data.location_id,
    tags: props.asset.data.tags?.map(tag => tag.id) || [],
    image: null,
    custom_id: props.asset.data.custom_id,
    additional_files: [],
    status: props.asset.data.status || 'Available' as Status,
    remove_files: []
});

const formatFileSize = (bytes: number) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};


const imagePreview = ref(props.asset.data.image ? `/storage/${props.asset.data.image}` : null);
const fileName = ref('');

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 4 * 1024 * 1024) {
            alert('Image size should not exceed 4MB');
            return;
        }
        form.image = file;
        fileName.value = file.name;
        console.log(form.image);
        console.log('IMAGE NAME' + fileName.value);

        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const handleDrop = (e) => {
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


const handleAdditionalFiles = (e) => {
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

    form.post(route('assets.update', props.asset.data.id), {
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
    <AppLayout title="Edit Asset">
        <template #header>
            <div class="flex gap-4 items-center">
                <div class="flex gap-4 justify-between items-center w-full">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Edit Asset
                    </h2>

                </div>
                <Link :href="route('assets.index')"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-gray-800 rounded-md border border-transparent transition duration-150 ease-in-out dark:bg-gray-200 dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                Cancel
                </Link>
            </div>

        </template>
        <Container>
            <form @submit.prevent="submit" class="space-y-6">

                <div>
                    <StatusSelector v-model="form.status" :error="form.errors.status" />
                </div>

                <div>
                    <InputLabel for="custom_id" value="Custom ID" />
                    <TextInput id="custom_id" v-model="form.custom_id" type="text" class="block mt-1 w-full" />
                    <InputError :message="form.errors.custom_id" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput id="name" v-model="form.name" type="text" class="block mt-1 w-full" />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="value" value="Value" />
                    <TextInput id="value" v-model="form.value" type="number" step="0.01" class="block mt-1 w-full" />
                    <InputError :message="form.errors.value" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="description" value="Description" />
                    <textarea id="description" v-model="form.description"
                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        rows="4"></textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>

                <div>
                    <InputLabel value="Image" />
                    <div @drop.prevent="handleDrop" @dragover.prevent
                        class="flex justify-center px-6 pt-5 pb-6 mt-1 rounded-md border-2 border-gray-300 border-dashed">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto w-12 h-12 text-gray-400" stroke="currentColor" fill="none"
                                viewBox="0 0 48 48">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="image-upload"
                                    class="relative font-medium text-orange-600 bg-white rounded-md cursor-pointer focus-within:outline-none focus-within:ring-2 focus-within:ring-orange-500 focus-within:ring-offset-2 hover:text-orange-500">
                                    <span>Upload a file</span>
                                    <input id="image-upload" type="file" class="sr-only" accept="image/png,image/jpeg"
                                        @change="handleImageUpload" />
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG up to 4MB</p>
                        </div>
                    </div>
                    <div v-if="imagePreview" class="flex items-center p-4 mt-3 bg-gray-50 rounded-md">
                        <img :src="imagePreview" class="object-cover w-16 h-16 rounded" />
                        <div class="ml-4">
                            <p class="text-sm text-gray-700">
                                {{ form.image ? 'form:' + form.image.name : fileName || 'Current image' }}
                            </p>
                        </div>
                    </div>
                    <InputError :message="form.errors.image" class="mt-2" />
                </div>


                <!-- Tag/Location/Category-->
                <div>
                    <InputLabel for="category" value="Category" />
                    <CustomMultiselect id="category" v-model="form.category_id" :options="categories.data" label="name"
                    value-prop="id" placeholder="Select a category" class="mt-1" />
                    <InputError :message="form.errors.category_id" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="location" value="Location" />
                    <CustomMultiselect id="location" v-model="form.location_id" :options="locations.data" label="name"
                    value-prop="id" placeholder="Select a location" class="mt-1" />
                    <InputError :message="form.errors.location_id" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="tags" value="Tags" />
                    <div class="relative">

                        <SearchMultiselect id="tags" v-model="form.tags" :options="tags.data" label="name"
                        value-prop="id" :multiple=true placeholder="Search and select tags" class="mt-1" />
                    </div>
                    <InputError :message="form.errors.tags" class="mt-2" />
                </div>

                <div>
                    <InputLabel value="Additional Files" />
                    <div class="mt-1">
                        <input type="file" multiple @change="handleAdditionalFiles" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100"/>
                        <p class="mt-1 text-xs text-gray-500">Upload multiple files (PNG, JPG, PDF up to 4MB each)</p>
                    </div>
                    <!-- Display existing files -->
                    <div v-if="existingFiles.length > 0" class="mt-3 space-y-2">
                        <h4 class="text-sm font-medium text-gray-700">Existing Files:</h4>
                        <div v-for="file in existingFiles" :key="file.id" class="flex justify-between items-center p-2 bg-gray-50 rounded-md">
                            <div class="ml-2">
                                <p class="text-sm text-gray-700">{{ file.name }}</p>
                                <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
                            </div>
                            <button type="button" @click="removeExistingFile(file.id)" class="px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 focus:outline-none">
                                Remove
                            </button>
                        </div>
                    </div>

                    <!-- Display newly uploaded files -->
                    <div v-if="form.additional_files.length > 0" class="mt-3 space-y-2">
                        <h4 class="text-sm font-medium text-gray-700">New Files:</h4>
                        <div v-for="(file, index) in form.additional_files" :key="index" class="flex justify-between items-center p-2 bg-gray-50 rounded-md">
                            <div class="ml-2">
                                <p class="text-sm text-gray-700">{{ file.name }}</p>
                                <p class="text-xs text-gray-500">{{ (file.size / 1024 / 1024).toFixed(2) }} MB</p>
                            </div>
                            <button type="button" @click="removeNewFile(index)" class="px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 focus:outline-none">
                                Remove
                            </button>
                        </div>
                    </div>
                    <InputError :message="form.errors.additional_files" class="mt-2" />
                </div>


                <div class="flex justify-end">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update Asset
                    </PrimaryButton>
                </div>
            </form>
        </Container>
    </AppLayout>
</template>
