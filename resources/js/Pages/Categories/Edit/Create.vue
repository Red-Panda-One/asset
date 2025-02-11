<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const colorPicker = ref(null);

const form = useForm({
    name: '',
    description: '',
    color: '#646a75'
});

const generateRandomColor = () => {
    const randomColor = '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0');
    form.color = randomColor;
};

const openColorPicker = () => {
    colorPicker.value.click();
};

const createCategory = () => {
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
                                    <TextInput
                                        id="color"
                                        v-model="form.color"
                                        type="text"
                                        class="flex-1 cursor-pointer"
                                        @click="openColorPicker"
                                    />
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
