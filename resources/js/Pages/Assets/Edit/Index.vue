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

const form = useForm({
    name: props.asset.data.name,
    description: props.asset.data.description,
    value: props.asset.data.value,
    category_id: props.asset.data.category_id,
    location_id: props.asset.data.location_id,
    tags: props.asset.data.tags?.map(tag => tag.id) || [],
    image: null,
    status: props.asset.data.status || 'Available' as Status
});

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



                <div class="flex justify-end">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update Asset
                    </PrimaryButton>
                </div>
            </form>
        </Container>
    </AppLayout>
</template>
