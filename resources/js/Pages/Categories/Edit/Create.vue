<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

interface CategoryForm {
    name: string;
    description: string;
    color: string;
}

const colorPicker = ref<HTMLInputElement | null>(null);

const form = useForm<CategoryForm>({
    name: '',
    description: '',
    color: '#646a75'
});

const generateRandomColor = (): void => {
    const randomColor = '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0');
    form.color = randomColor;
};

const openColorPicker = (): void => {
    colorPicker.value?.click();
};

const createCategory = (): void => {
    form.post(route('categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout title="Create Tag">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Create Category
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 lg:p-8">
                        <form @submit.prevent="createCategory">
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="block mt-1 w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="description" value="Description (optional)" />
                                <TextInput
                                    id="description"
                                    v-model="form.description"
                                    type="text"
                                    class="block mt-1 w-full"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>
                            <div class="mt-4">
                                <InputLabel for="color" value="Color" />
                                <div class="flex gap-2 items-center">
                                    <button
                                        type="button"
                                        :style="{ backgroundColor: form.color }"
                                        class="px-4 py-2 text-white rounded-md transition-colors"
                                        @click="generateRandomColor"
                                    >
                                        Random Color
                                    </button>
                                    <input
                                        type="color"
                                        v-model="form.color"
                                        id="colorPicker"
                                        ref="colorPicker"
                                        class="hidden"
                                    />
                                    <div class="relative">
                                        <TextInput
                                            id="color"
                                            v-model="form.color"
                                            type="text"
                                            class="w-[120px]"
                                        />
                                        <button
                                            type="button"
                                            class="absolute right-2 top-1/2 -translate-y-1/2"
                                            @click="openColorPicker"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm1 2h10v10H5V4z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                    </div>
                                </div>
                                <InputError :message="form.errors.color" class="mt-2" />
                            </div>

                            <div class="flex justify-end mt-6">
                                <SecondaryButton
                                    @click="$inertia.visit(route('categories.index'))"
                                    class="mr-3"
                                    type="button"
                                >
                                    Cancel
                                </SecondaryButton>

                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Create
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
