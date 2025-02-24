<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import Table from '@/Components/Table.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { PhotoIcon } from '@heroicons/vue/24/solid';
import NeumorphicBadge from '@/Components/NeumorphicBadge.vue';

interface Kit {
    id: string;
    name: string;
    description: string;
    image?: string;
    asset_count: number;
}

interface Column {
    key: keyof Kit;
    label: string;
}

interface Filters {
    search?: string;
    per_page?: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationMeta {
    from: number;
    to: number;
    total: number;
    links: PaginationLink[];
}

interface KitsData {
    data: Kit[];
    meta: PaginationMeta;
}

const props = withDefaults(defineProps<{
    kits: KitsData;
    filters?: Filters;
}>(), {
    filters: () => ({
        search: '',
        per_page: '20'
    })
});

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'description', label: 'Description' },
    { key: 'asset_count', label: 'Assets' },
    { key: 'status', label: 'Status' }, // New column
];

const handleAdd = () => {
    router.visit(route('kits.create'));
};

const handleEdit = (kit: Kit) => {
    console.log(kit.id);

    router.visit(route('kits.edit', kit.id));
};


const confirmingKitDeletion = ref(false);
const kitToDelete = ref(null);

const confirmKitDeletion = (kit) => {
    kitToDelete.value = kit;
    confirmingKitDeletion.value = true;
};

const deleteKit = () => {
    router.delete(route('kits.destroy', kitToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingKitDeletion.value = false;
            kitToDelete.value = null;
        },
    });
};

const search = ref(props.filters.search || '');
const perPage = ref(props.filters.per_page || '20');

watch([search, perPage], ([newSearch, newPerPage]) => {
    router.get(
        route('kits.index'),
        { search: newSearch, per_page: newPerPage },
        { preserveState: true, preserveScroll: true }
    );
});

console.log(props.kits);

</script>

<template>
    <AppLayout title="Kits">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Kits
            </h2>
        </template>
        <Container>
            <Table
                title="Kits"
                description="A list of all kits in your organization."
                :columns="columns"
                :items="kits.data"
                @cta="handleAdd"
                @edit="handleEdit"
                @delete="confirmKitDeletion"
            >
                <template #header>
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex gap-4 items-center">
                            <div class="flex-1">
                                <input
                                    v-model="search"
                                    type="search"
                                    placeholder="Search kits..."
                                    class="block py-1.5 w-full text-gray-900 rounded-md border-0 ring-1 ring-inset ring-gray-300 shadow-sm placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-gray-200 dark:ring-gray-600 dark:placeholder:text-gray-400"
                                />
                            </div>
                            <select
                                v-model="perPage"
                                class="py-1.5 pr-10 pl-3 text-gray-900 rounded-md border-0 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-orange-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-gray-200 dark:ring-gray-600"
                            >
                                <option value="10">10 per page</option>
                                <option value="20">20 per page</option>
                                <option value="50">50 per page</option>
                                <option value="100">100 per page</option>
                            </select>
                        </div>
                    </div>
                </template>

                <template #cell-name="{ item }">
                    <div class="flex items-center cursor-pointer" @click="router.visit(route('kits.show', item.id))">
                        <div v-if="item.image" class="flex-shrink-0 mr-4 w-12 h-12">
                            <img :src="`/storage/${item.image}`" class="object-cover w-12 h-12 rounded-full" />
                        </div>
                        <div v-else class="flex justify-center items-center mr-4 w-12 h-12 bg-gray-100 rounded-lg dark:bg-gray-700">
                                <PhotoIcon class="w-6 h-6 text-gray-400" />
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

                <template #cell-asset_count="{ item }">
                    <div class="text-gray-600 dark:text-gray-400">{{ item.asset_count }}</div>
                </template>

                <template #cell-status="{ item }">
                    <div class="text-gray-600 dark:text-gray-400">{{ item.status }}</div>
                </template>

                <template #pagination>
                    <div v-if="kits.meta" class="px-4 py-3 sm:px-6">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ kits.meta.from }} to {{ kits.meta.to }} of {{ kits.meta.total }} results
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in kits.meta.links"
                                    :key="link.label"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="px-3 py-1 rounded-md"
                                    :class="{
                                        'bg-orange-600 text-white': link.active,
                                        'text-gray-600 dark:text-gray-400': !link.active,
                                        'opacity-50 cursor-not-allowed': !link.url
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </Table>
            <!-- Delete Confirmation Modal -->
            <ConfirmationModal :show="confirmingKitDeletion" @close="confirmingKitDeletion = false">
                <template #title>
                    Delete Kit
                </template>

                <template #content>
                    Are you sure you want to delete this kit? This action cannot be undone.
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingKitDeletion = false">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': false }"
                        @click="deleteKit"
                    >
                        Delete
                    </PrimaryButton>
                </template>
            </ConfirmationModal>
        </Container>
    </AppLayout>
</template>
