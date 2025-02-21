<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import { PhotoIcon } from '@heroicons/vue/24/solid';
import { computed } from 'vue';
import type { KitResponse } from '@/types/kit';

interface Props {
    kit: KitResponse;
}

const props = defineProps<Props>();
</script>

<template>
    <AppLayout :title="props.kit.data.name">
        <template #header>
            <div class="flex gap-4 items-center">
                <div class="flex-shrink-0 w-12 h-12">
                    <img
                        v-if="props.kit.data.image"
                        :src="`/storage/${props.kit.data.image}`"
                        class="object-cover w-12 h-12 rounded-lg"
                        alt="Kit image"
                    />
                    <div v-else class="flex justify-center items-center w-12 h-12 bg-gray-100 rounded-lg dark:bg-gray-700">
                        <PhotoIcon class="w-8 h-8 text-gray-400" />
                    </div>
                </div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ props.kit.data.name }}
                </h2>
            </div>
        </template>

        <Container>
            <div class="overflow-hidden bg-white shadow sm:rounded-lg dark:bg-gray-800">
                <div class="px-4 py-6 sm:px-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ props.kit.data.id }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                {{ new Date(props.kit.data.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }) }}
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                {{ props.kit.data.description || '-' }}
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Image</dt>
                            <dd class="mt-1">
                                <img
                                    v-if="props.kit.data.image"
                                    :src="`/storage/${props.kit.data.image}`"
                                    class="object-cover w-48 h-48 rounded-lg"
                                    alt="Kit image"
                                />
                                <div v-else class="flex justify-center items-center w-48 h-48 bg-gray-100 rounded-lg dark:bg-gray-700">
                                    <PhotoIcon class="w-16 h-16 text-gray-400" />
                                </div>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
