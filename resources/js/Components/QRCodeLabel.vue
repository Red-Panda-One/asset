<script setup lang="ts">
import { useQRCode } from '@vueuse/integrations/useQRCode';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import defaultColor from '@/../svg/label-default-colored.svg';
import defaultBW from '@/../svg/label-default-bw.svg';

interface Props {
    id: string;
    name: string;
    type: "ASSET" | "KIT";
    url: string;
}

const props = defineProps<Props>();
const colorMode = ref('color');

const qrcode = useQRCode(props.url, {
    errorCorrectionLevel: 'H',
    margin: 0,
    width: 200,
});

const page = usePage();

const downloadQR = (): void => {
    const link = document.createElement('a');
    link.download = `${props.type? props.type: "ITEM" }-${props.name}-${props.id}-qr.png`;
    link.href = qrcode.value;
    link.click();
};

const printLabel = (): void => {
    const printWindow = window.open('', '_blank');
    if (!printWindow) return;
    const team = (page.props.auth as any).user.current_team;
    const logoSrc = colorMode.value === 'color' ? (team.colored_logo || defaultColor) : (team.bw_logo || defaultBW);
    printWindow.document.write(`
        <html>
            <head>
                <title>Label - ${props.name}</title>
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
                    .item-type {
                        font-family: Arial, sans-serif;
                        font-size: 8pt;
                        color: #374151;
                        margin: 0;
                    }
                    .item-name {
                        font-family: Arial, sans-serif;
                        font-size: 12pt;
                        font-weight: bold;
                        color: #000;
                        margin: 0;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .item-id {
                        font-family: Arial, sans-serif;
                        font-size: 10pt;
                        color: #374151;
                        margin: 0;
                    }
                    .qr-section {
                        width: 1.1in;
                        height: 100%;
                        display: flex;
                        align-items: center;
                        flex-shrink: 0;
                        padding-left: 0.1in;
                        position: relative;
                    }
                    .qr-section img {
                        width: 1.0in;
                        height: 1.0in;
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
                        <img src="${qrcode.value}" alt="QR Code">
                        <img src="${logoSrc || defaultBW}" alt="Thermal Label Icon" class="thermal-icon">
                    </div>
                    <div class="info-section">
                        <p class="property-text">PROPERTY OF</p>
                        <p class="team-name">${team.name}</p>
                        <p class="item-type">${props.type? props.type: "ITEM" }</p>
                        <p class="item-name">${props.name}</p>
                        <p class="item-id">${props.id}</p>
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
    <div class="flex flex-col items-center space-y-4">
        <div class="relative">
            <img :src="qrcode" :alt="`QR Code for ${name}`" class="w-48 h-48">
            <img :src="colorMode === 'color' ? ((page.props.auth as any).user.current_team.colored_logo || defaultColor) : ((page.props.auth as any).user.current_team.bw_logo || defaultBW)"
                alt="Team Logo"
                class="absolute top-1/2 left-1/2 z-10 w-14 h-14 transform -translate-x-1/2 -translate-y-1/2">
        </div>
        <div class="flex flex-col gap-4 items-center">
            <div class="flex gap-2 items-center">
                <label class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" v-model="colorMode" :true-value="'color'" :false-value="'bw'" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{ colorMode === 'color' ? 'Color' : 'Black & White' }}</span>
                </label>
            </div>
            <div class="flex gap-4">
                <button
                    @click="downloadQR"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Download QR Code
                </button>
                <button
                    @click="printLabel"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Print Label 1.1x3.5 in
                </button>
            </div>
        </div>
    </div>
</template>
