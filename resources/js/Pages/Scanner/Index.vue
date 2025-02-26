<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import QrScanner from 'qr-scanner';

interface QrResult {
    data: string;
}

const videoElement = ref<HTMLVideoElement | null>(null);
const qrScanner = ref<QrScanner | null>(null);
const scannerActive = ref<boolean>(false);
const error = ref<string>('');

const startScanner = async (): Promise<void> => {
    try {
        console.log('Initializing QR scanner...');
        error.value = '';
        qrScanner.value = new QrScanner(
            videoElement.value as HTMLVideoElement,
            result => {
                console.log('QR code detected:', result.data);
                if (result.data) {
                    try {
                        console.log('Parsing QR code data...');
                        let id;
                        let type;

                        try {
                            // First try parsing as URL
                            const url = new URL(result.data);
                            const pathSegments = url.pathname.split('/');
                            id = pathSegments[pathSegments.length - 1];
                            // Determine if it's a kit or asset URL
                            type = pathSegments.includes('kits') ? 'kits' : 'assets';
                        } catch (urlError) {
                            // If URL parsing fails, try extracting ID directly
                            const segments = result.data.split('/');
                            id = segments[segments.length - 1];
                            type = segments.includes('kits') ? 'kits' : 'assets';
                        }

                        console.log('Extracted ID:', id, 'Type:', type);

                        if (id) {
                            const routeName = type === 'kits' ? 'kits.show' : 'assets.show';
                            console.log('Navigating to:', route(routeName, id));
                            router.visit(route(routeName, id));
                        } else {
                            console.error('Invalid QR code: No valid ID found');
                            error.value = 'Invalid QR code format: No valid ID found';
                        }
                    } catch (e) {
                        console.error('Error processing QR code:', e);
                        error.value = 'Invalid QR code format';
                    }
                }
            },
            { highlightScanRegion: true }
        );
        console.log('Starting camera...');
        await qrScanner.value.start();
        console.log('Camera started successfully');
        scannerActive.value = true;
    } catch (e) {
        console.error('Camera initialization error:', e);
        error.value = 'Failed to start camera: ' + e.message;
    }
};

const stopScanner = (): void => {
    if (qrScanner.value) {
        qrScanner.value.stop();
        qrScanner.value.destroy();
        qrScanner.value = null;
        scannerActive.value = false;
    }
};

onMounted(() => {
    startScanner();
});

onUnmounted(() => {
    stopScanner();
});
</script>

<template>
    <AppLayout title="QR Scanner">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                QR Scanner
            </h2>
        </template>
        <Container>
            <div class="flex flex-col justify-center items-center space-y-4">
                <div class="overflow-hidden relative w-full max-w-lg bg-gray-100 rounded-lg aspect-video dark:bg-gray-800">
                    <video
                        ref="videoElement"
                        class="object-cover w-full h-full"
                    ></video>
                </div>

                <div v-if="error" class="text-red-600 dark:text-red-400">
                    {{ error }}
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Position the QR code within the camera view to scan
                </div>
            </div>
        </Container>
    </AppLayout>
</template>
