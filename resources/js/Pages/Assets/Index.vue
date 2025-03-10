<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import Table from '@/Components/Table.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import NeumorphicBadge from '@/Components/NeumorphicBadge.vue';
import { PhotoIcon } from '@heroicons/vue/24/solid';
import type { Asset, AssetsResponse, AssetFilters } from '@/types/asset';

declare function route(name: 'assets.create'): string;
declare function route(name: 'assets.edit', id: string): string;
declare function route(name: 'assets.destroy', id: string): string;
declare function route(name: 'assets.index'): string;
declare function route(name: 'assets.show', id: string): string;

interface Props {
    assets: AssetsResponse;
    filters: AssetFilters;
}

const props = defineProps<Props>();

const columns = [
    { key: 'custom_id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'value', label: 'Value' },
    { key: 'category', label: 'Category' },
    { key: 'tags', label: 'Tags' },
    { key: 'location', label: 'Location' },
    { key: 'description', label: 'Description' },
    { key: 'status', label: 'Status' }, 
];

const handleAdd = (): void => {
    router.visit(route('assets.create'));
};

const handleEdit = (asset: Asset): void => {
    router.visit(route('assets.edit', asset.id));
};

const confirmingAssetDeletion = ref(false);
const assetToDelete = ref<Asset | null>(null);

const confirmAssetDeletion = (asset: Asset): void => {
    assetToDelete.value = asset;
    confirmingAssetDeletion.value = true;
};

const deleteAsset = (): void => {
    if (!assetToDelete.value) return;

    router.delete(route('assets.destroy', assetToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingAssetDeletion.value = false;
            assetToDelete.value = null;
        },
    });
};

const search = ref<string>(props.filters.search || '');
const perPage = ref<string>(props.filters.per_page || '20');

watch([search, perPage], ([newSearch, newPerPage]) => {
    router.get(
        route('assets.index'),
        { search: newSearch, per_page: newPerPage },
        { preserveState: true, preserveScroll: true }
    );
});
</script>

<template>
    <AppLayout title="Assets">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Assets
            </h2>
        </template>
        <Container>
            <Table title="Assets" description="A list of all assets in your organization." :columns="columns"
                :items="assets.data" @cta="handleAdd" @edit="handleEdit" @delete="confirmAssetDeletion">
                <template #header>
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex gap-4 items-center">
                            <div class="flex-1">
                                <input v-model="search" type="search" placeholder="Search assets..."
                                    class="block py-1.5 w-full text-gray-900 rounded-md border-0 ring-1 ring-inset ring-gray-300 shadow-sm placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-gray-200 dark:ring-gray-600 dark:placeholder:text-gray-400" />
                            </div>
                            <select v-model="perPage"
                                class="py-1.5 pr-10 pl-3 text-gray-900 rounded-md border-0 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-orange-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-gray-200 dark:ring-gray-600">
                                <option value="10">10 per page</option>
                                <option value="20">20 per page</option>
                                <option value="50">50 per page</option>
                                <option value="100">100 per page</option>
                            </select>
                        </div>
                    </div>
                </template>


                <template #cell-name="{ item }">
                    <div class="flex items-center cursor-pointer" @click="router.visit(route('assets.show', item.id))">
                        <div class="flex-shrink-0 mr-4 w-10 h-10">
                            <img v-if="item.image" :src="`/storage/${item.image}`"
                                class="object-cover w-10 h-10 rounded-lg" alt="Asset image" />
                            <div v-else
                                class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-700">
                                <PhotoIcon class="w-6 h-6 text-gray-400" />
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="text-orange-600 hover:text-orange-700">{{ item.name }}</div>
                            <NeumorphicBadge
                                :color="item.status === 'Available' ? '#D8F1E2' :
                                    ['Retired', 'Lost', 'Faulty'].includes(item.status) ? '#FFE5E5' : '#D8E9FE'"
                                :text="item.status"
                                class="text-xs"
                            />
                        </div>
                    </div>
                </template>

                <template #cell-value="{ item }">
                    {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(item.value || 0) }}
                </template>

                <template #cell-category="{ item }">
                    <NeumorphicBadge v-if="item.category" color="#E6E6FA" :text="item.category.name" />
                    <span v-else>-</span>
                </template>

                <template #cell-tags="{ item }">
                    <div class="flex flex-wrap gap-1">
                        <NeumorphicBadge v-for="tag in item.tags" :key="tag.id" color="#FFE4E1" :text="tag.name" />
                        <span v-if="!item.tags?.length">-</span>
                    </div>
                </template>

                <template #cell-location="{ item }">
                    {{ item.location?.name || '-' }}
                </template>

                <template #cell-custom_id="{ item }">
                    <div class="text-gray-600 dark:text-gray-400">{{ item.custom_id }}</div>
                </template>

                <template #cell-status="{ item }">
                    <div class="text-gray-600 dark:text-gray-400">{{ item.status }}</div>
                </template>

                <template #pagination>
                    <div v-if="assets.meta" class="px-4 py-3 sm:px-6">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ assets.meta.from }} to {{ assets.meta.to }} of {{ assets.meta.total }}
                                results
                            </div>
                            <div class="flex space-x-2">
                                <Link v-for="link in assets.meta.links" :key="link.label" :href="link.url"
                                    v-html="link.label" class="px-3 py-1 rounded-md" :class="{
                'bg-orange-600 text-white': link.active,
                'text-gray-600 dark:text-gray-400': !link.active,
                'opacity-50 cursor-not-allowed': !link.url
            }" />
                            </div>
                        </div>
                    </div>
                </template>
            </Table>

            <ConfirmationModal :show="confirmingAssetDeletion" @close="confirmingAssetDeletion = false">
                <template #title>
                    Delete Asset
                </template>

                <template #content>
                    Are you sure you want to delete this asset? This action cannot be undone.
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingAssetDeletion = false">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': false }" @click="deleteAsset">
                        Delete
                    </PrimaryButton>
                </template>
            </ConfirmationModal>
        </Container>
    </AppLayout>
</template>
