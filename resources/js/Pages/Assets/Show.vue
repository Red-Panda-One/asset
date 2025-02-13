<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import NeumorphicBadge from '@/Components/NeumorphicBadge.vue';
import { PhotoIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    asset: Object,
});


console.log(props.asset.data);
console.log(props.asset.data.created_at);
console.log(props.asset.data.updated_at);
</script>

<template>
    <AppLayout :title="props.asset.data.name">
        <template #header>
            <div class="flex gap-4 items-center">
                <div class="flex-shrink-0 w-12 h-12">
                    <img
                        v-if="props.asset.data.image"
                        :src="`/storage/${props.asset.data.image}`"
                        class="object-cover w-12 h-12 rounded-lg"
                        alt="Asset image"
                    />
                    <div v-else class="flex justify-center items-center w-12 h-12 bg-gray-100 rounded-lg dark:bg-gray-700">
                        <PhotoIcon class="w-8 h-8 text-gray-400" />
                    </div>
                </div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ props.asset.data.name }}
                </h2>
            </div>
        </template>

        <Container>
            <div class="overflow-hidden bg-white shadow sm:rounded-lg dark:bg-gray-800">
                <div class="px-4 py-6 sm:px-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ props.asset.data.id }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                {{ new Date(props.asset.data.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }) }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                            <dd class="mt-1">
                                <NeumorphicBadge
                                    v-if="props.asset.data.category"
                                    color="#E6E6FA"
                                    :text="props.asset.data.category.name"
                                />
                                <span v-else class="text-sm text-gray-900 dark:text-gray-200">-</span>
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                {{ props.asset.data.location?.name || '-' }}
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tags</dt>
                            <dd class="mt-1">
                                <div class="flex flex-wrap gap-1">
                                    <NeumorphicBadge
                                        v-for="tag in props.asset.data.tags"
                                        :key="tag.id"
                                        color="#FFE4E1"
                                        :text="tag.name"
                                    />
                                    <span v-if="!props.asset.data.tags?.length" class="text-sm text-gray-900 dark:text-gray-200">-</span>
                                </div>
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Value</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(props.asset.data.value || 0) }}
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                {{ props.asset.data.description || '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
