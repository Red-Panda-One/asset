<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    tag: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.tag.data.name,
    description: props.tag.data.description || ''
});

const updateTag = () => {
    form.patch(route('tags.update', props.tag.data.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout title="Edit Tag">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Tag
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 lg:p-8">
                        <form @submit.prevent="updateTag">
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

                            <div class="flex justify-end mt-6">
                                <SecondaryButton
                                    @click="$inertia.visit(route('tags.index'))"
                                    class="mr-3"
                                    type="button"
                                >
                                    Cancel
                                </SecondaryButton>

                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Update
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
