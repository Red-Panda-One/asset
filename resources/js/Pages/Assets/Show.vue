<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import NeumorphicBadge from '@/Components/NeumorphicBadge.vue';
import { PhotoIcon, DocumentIcon } from '@heroicons/vue/24/solid';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { computed, ref } from 'vue';
import QRCodeLabel from '@/Components/QRCodeLabel.vue';
import type { AssetResponse } from '@/types/asset';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

declare function route(name: 'assets.show', id: string): string;
declare function route(name: 'assets.edit', id: string): string;

interface Props {
    asset: AssetResponse;
}

const props = defineProps<Props>();

const assetUrl = computed(() => route('assets.show', props.asset.data.id));

const formatFileSize = (bytes: number) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const previewFile = ref<{ name: string; file_path: string; type: string } | null>(null);

const getFileType = (fileName: string) => {
    const extension = fileName.split('.').pop()?.toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) return 'image';
    if (extension === 'pdf') return 'pdf';
    return 'other';
};

const handlePreview = (file: any) => {
    previewFile.value = {
        name: file.name,
        file_path: file.file_path,
        type: getFileType(file.name)
    };
};

const closePreview = () => {
    previewFile.value = null;
};
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
                <div class="flex gap-4 justify-between items-center w-full">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        {{ props.asset.data.name }}
                    </h2>
                    <PrimaryButton
                        @click="router.visit(route('assets.edit', props.asset.data.id))"
                        class=""
                    >
                        Edit Asset
                    </PrimaryButton>

                </div>
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
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Custom ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ props.asset.data.custom_id || '-' }}</dd>
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
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Additional Files</dt>
                            <dd class="mt-1">
                                <div class="space-y-2">
                                    <div v-for="file in props.asset.data.additional_files" :key="file.id"
                                         class="flex justify-between items-center p-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                                        <div class="flex items-center gap-3">
                                            <DocumentIcon class="w-5 h-5 text-gray-400" />
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ file.name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatFileSize(file.size) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button
                                                @click="handlePreview(file)"
                                                class="px-3 py-1 text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                                            >
                                                Preview
                                            </button>
                                            <a
                                                :href="`/storage/${file.file_path}`"
                                                target="_blank"
                                                class="px-3 py-1 text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                                            >
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                    <div v-if="!props.asset.data.additional_files?.length" class="text-sm text-gray-500 dark:text-gray-400">
                                        No additional files
                                    </div>
                                </div>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">QR Code</dt>
                            <dd class="mt-1">
                                <QRCodeLabel
                                    :id="props.asset.data.id"
                                    :name="props.asset.data.name"
                                    :url="assetUrl"
                                    type="ASSET"
                                />
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </Container>
        <!-- File Preview Modal -->
        <Modal :show="!!previewFile" @close="closePreview" maxWidth="4xl">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ previewFile?.name }}</h2>
                    <button @click="closePreview" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="relative">
                    <img
                        v-if="previewFile?.type === 'image'"
                        :src="`/storage/${previewFile.file_path}`"
                        class="max-w-full h-auto rounded-lg"
                        :alt="previewFile.name"
                    />
                    <iframe
                        v-else-if="previewFile?.type === 'pdf'"
                        :src="`/storage/${previewFile.file_path}`"
                        class="w-full h-[80vh] rounded-lg"
                    ></iframe>
                    <div
                        v-else
                        class="p-4 text-center text-gray-500 dark:text-gray-400"
                    >
                        Preview not available for this file type
                    </div>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
