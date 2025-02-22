<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import { PhotoIcon } from '@heroicons/vue/24/solid';
import { ref, computed } from 'vue';
import type { KitResponse } from '@/types/kit';
import Modal from '@/Components/Modal.vue';
import Table from '@/Components/Table.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { router } from '@inertiajs/vue3';
import QRCodeLabel from '@/Components/QRCodeLabel.vue';

declare function route(name: 'kits.show', id: string): string;

interface Props {
    kit: KitResponse;
    assets?: { data: Array<{
        id: string;
        name: string;
        category?: { name: string };
        location?: { name: string };
    }>};
    kitAssets?: { data: Array<{
        id: string;
        kit_id: string;
    }> };
    unavailableAssets?:  Array<{
        kit_id: string;
        asset_id: string;
    }>;
}


const props = defineProps<Props>();

const showManageModal = ref(false);
const searchQuery = ref('');
const availableAssets = ref([]);


const columns = [
    { key: 'name', label: 'Name' },
    { key: 'category', label: 'Category' },
    { key: 'location', label: 'Location' },
];

const filteredAssets = computed(() => {
    if (!searchQuery.value) return availableAssets.value;
    const query = searchQuery.value.toLowerCase();
    return availableAssets.value.filter(asset =>
        asset.name.toLowerCase().includes(query) ||
        asset.category?.name.toLowerCase().includes(query) ||
        asset.location?.name.toLowerCase().includes(query)
    );
});


const handleManageAssets = () => {
    showManageModal.value = true;
    availableAssets.value = props.assets.data;
};

const handleAddAsset = (asset: any) => {
    router.post(route('kits.assets.add', props.kit.data.id), {
        asset_id: asset.id
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showManageModal.value = false;
        }
    });
};

const handleRemoveAsset = (asset: any) => {
    router.delete(route('kits.assets.destroy', [props.kit.data.id, asset.id]), {
        preserveScroll: true
    });
};

const isAssetInKit = (asset) => {
    return props.kitAssets?.data?.some(kitAsset => kitAsset.id === asset.id) || false;
};

const isAssetInOtherKit = (asset) => {
    return props.unavailableAssets?.some(kitAsset =>
        kitAsset.asset_id === asset.id && kitAsset.kit_id !== props.kit.data.id
    ) || false;
};

const getLinkedKitId = (asset) => {
    const linkedAsset = props.unavailableAssets?.find(kitAsset =>
        kitAsset.asset_id === asset.id && kitAsset.kit_id !== props.kit.data.id
    );
    return linkedAsset?.kit_id;
};

const kitUrl = computed(() => route('kits.show', props.kit.data.id));

console.log(props.unavailableAssets);
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
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Assets Table Section -->
                <div class="md:col-span-2">
                    <div class="overflow-hidden bg-white shadow dark:bg-gray-800 sm:rounded-lg">
                        <div>
                            <Table
                                title="Assets in Kit"
                                :columns="columns"
                                :items="props.kitAssets.data || []"
                                :showEdit="false"
                                class="w-full"
                                ctaText="Manage Assets"
                                deleteText="Remove from Kit"
                                @delete="handleRemoveAsset"
                                @cta="handleManageAssets"
                            />
                        </div>
                    </div>
                </div>

                <!-- Kit Details Section -->
                <div class="md:col-span-1">
                    <div class="overflow-hidden bg-white shadow sm:rounded-lg dark:bg-gray-800">
                        <div class="px-4 py-6 sm:px-6">
                            <dl class="space-y-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ props.kit.data.id }}</dd>
                                </div>
                                <div>
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
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                        {{ props.kit.data.description || '-' }}
                                    </dd>
                                </div>
                                <div>
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
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">QR Code</dt>
                                    <dd class="mt-1">
                                        <QRCodeLabel
                                            :id="props.kit.data.id"
                                            :name="props.kit.data.name"
                                            :url="kitUrl"
                                            type="KIT"
                                        />
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </Container>

        <!-- Manage Assets Modal -->
        <Modal :show="showManageModal" @close="showManageModal = false">
            <div class="p-6">
                <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Manage Kit Assets</h2>
                <div class="mb-4">
                    <TextInput
                        v-model="searchQuery"
                        type="text"
                        class="w-full"
                        placeholder="Search assets..."
                    />
                </div>
                <div class="space-y-2">
                    <div v-for="asset in filteredAssets" :key="asset.id"
                         class="flex justify-between items-center p-3 bg-gray-50 rounded-lg dark:bg-gray-700">
                        <div>
                            <p class="font-medium" :class="{
                                'text-gray-900 dark:text-gray-100': !isAssetInOtherKit(asset),
                                'text-red-600 dark:text-red-400': isAssetInOtherKit(asset)
                            }">{{ asset.name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ asset.category?.name || 'No category' }} • {{ asset.location?.name || 'No location' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <PrimaryButton
                                v-if="isAssetInOtherKit(asset)"
                                @click="router.visit(route('kits.show', getLinkedKitId(asset)))"
                                class="bg-orange-500 hover:bg-orange-300"
                            >
                                Go to Kit
                            </PrimaryButton>
                            <PrimaryButton
                                v-else
                                @click="isAssetInKit(asset) ? handleRemoveAsset(asset) : handleAddAsset(asset)"
                                :class="isAssetInKit(asset) ? 'bg-red-600 hover:bg-red-500' : 'bg-orange-600 hover:bg-orange-500'"
                            >
                                {{ isAssetInKit(asset) ? 'Remove from Kit' : 'Add to Kit' }}
                            </PrimaryButton>
                        </div>
                    </div>
                    <div v-if="filteredAssets.length === 0" class="py-4 text-center text-gray-500 dark:text-gray-400">
                        No assets found
                    </div>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
