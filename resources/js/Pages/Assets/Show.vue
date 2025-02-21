<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import NeumorphicBadge from '@/Components/NeumorphicBadge.vue';
import { PhotoIcon } from '@heroicons/vue/24/solid';
import { useQRCode } from '@vueuse/integrations/useQRCode';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import thermalIcon from '@/../svg/thermal-lable-icon.svg';
import type { AssetResponse } from '@/types/asset';

interface Props {
    asset: AssetResponse;
}

const props = defineProps<Props>();

const assetUrl = computed(() => route('assets.show', props.asset.data.id));
const qrcode = useQRCode(assetUrl, {
    errorCorrectionLevel: 'H',
    margin: 3,
    width: 200,
});

const downloadQR = (): void => {
    const link = document.createElement('a');
    link.download = `asset-${props.asset.data.id}-qr.png`;
    link.href = qrcode.value;
    link.click();
};

const page = usePage();

const printLabel = (): void => {
    const printWindow = window.open('', '_blank');
    if (!printWindow) return;
    printWindow.document.write(`
        <html>
            <head>
                <title>Asset Label - ${props.asset.data.name}</title>
                <style>
                    @page { size: 3.5in 1.1in; margin: 0; }
                    body {
                        margin: 0;
                        padding: 0;
                        height: 1.1in;
                        width: 3.5in;
                    }
                    .label-container {
                        display: flex;
                        height: 100%;
                    }
                    .info-section {
                        flex-grow: 1;
                        padding: 0.1in;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        border-left: 2px solid black;
                    }
                    .property-text {
                        font-family: Arial, sans-serif;
                        font-size: 8pt;
                        color: #374151;
                        margin: 0;
                    }
                    .team-name {
                        font-family: Arial, sans-serif;
                        font-size: 14pt;
                        font-weight: bold;
                        color: #000;
                        margin: 0;
                        margin-bottom: 0.1in;
                    }
                    .asset-name {
                        font-family: Arial, sans-serif;
                        font-size: 12pt;
                        font-weight: bold;
                        color: #000;
                        margin: 0;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .asset-id {
                        font-family: Arial, sans-serif;
                        font-size: 10pt;
                        color: #374151;
                        margin: 0;
                    }
                    .qr-section {
                        width: 1.1in;
                        height: 1.1in;
                        flex-shrink: 0;
                        padding-left: 0.05in;
                        position: relative;
                    }
                    .qr-section img {
                        width: 1.05in;
                        height: 1.05in;
                    }
                    .thermal-icon {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 30% !important;
                        height: 30% !important;
                        z-index: 10;
                    }
                </style>
            </head>
            <body>
                <div class="label-container">
                    <div class="qr-section">
                        <img src="${qrcode.value}" alt="Asset QR Code">
                        <img src="${thermalIcon}" alt="Thermal Label Icon" class="thermal-icon">
                    </div>
                    <div class="info-section">
                        <p class="property-text">PROPERTY OF</p>
                        <p class="team-name">${page.props.auth.user.current_team.name}</p>
                        <p class="asset-name">${props.asset.data.name}</p>
                        <p class="asset-id">${props.asset.data.id}</p>
                    </div>
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
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
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">QR Code</dt>
                            <dd class="mt-1">
                                <div class="flex flex-col items-center space-y-4">
                                    <img :src="qrcode" alt="Asset QR Code" class="w-48 h-48">
                                    <div class="flex gap-4">
                                        <button
                                            @click="downloadQR"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                        >
                                            Download QR Code
                                        </button>
                                        <button
                                            @click="printQR"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                        >
                                            Print QR Code
                                        </button>
                                        <button
                                            @click="printLabel"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                        >
                                            Print Label 1.1x3.5 in
                                        </button>
                                    </div>
                                </div>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
