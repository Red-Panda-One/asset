<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import Table from '@/Components/Table.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

interface Location {
    id: string;
    name: string;
    address: string;
    description: string;
    image?: string;
}

interface Column {
    key: keyof Location;
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

interface LocationsData {
    data: Location[];
    meta: PaginationMeta;
}

const props = defineProps<{
    tags: LocationsData;
    filters: Filters;
}>();

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'address', label: 'Address' },
    { key: 'description', label: 'Description' },
];

const handleAdd = () => {
    router.visit(route('locations.create'));
};

const handleEdit = (location: Location) => {
    router.visit(route('locations.edit', location.id));
};

const confirmingLocationDeletion = ref(false);
const locationToDelete = ref(null);

const confirmLocationDeletion = (location) => {
    locationToDelete.value = location;
    confirmingLocationDeletion.value = true;
};

const deleteLocation = () => {
    router.delete(route('locations.destroy', locationToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingLocationDeletion.value = false;
            locationToDelete.value = null;
        },
    });
};

const search = ref(props.filters.search || '');
const perPage = ref(props.filters.per_page || '20');

watch([search, perPage], ([newSearch, newPerPage]) => {
    router.get(
        route('locations.index'),
        { search: newSearch, per_page: newPerPage },
        { preserveState: true, preserveScroll: true }
    );
});
</script>

<template>
    <AppLayout title="Locations">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Locations
            </h2>
        </template>
        <Container>
            <Table
                title="Locations"
                description="A list of all locations in your organization."
                :columns="columns"
                :items="tags.data"
                @add="handleAdd"
                @edit="handleEdit"
                @delete="confirmLocationDeletion"
            >
                <template #header>
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex gap-4 items-center">
                            <div class="flex-1">
                                <input
                                    v-model="search"
                                    type="search"
                                    placeholder="Search locations..."
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
                    <div class="flex items-center">
                        <div v-if="item.image" class="flex-shrink-0 mr-4 w-10 h-10">
                            <img :src="`/storage/${item.image}`" class="object-cover w-10 h-10 rounded-full" />
                        </div>
                        <div>{{ item.name }}</div>
                    </div>
                </template>

                <template #pagination>
                    <div v-if="tags.meta" class="px-4 py-3 sm:px-6">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ tags.meta.from }} to {{ tags.meta.to }} of {{ tags.meta.total }} results
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in tags.meta.links"
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
            <ConfirmationModal :show="confirmingLocationDeletion" @close="confirmingLocationDeletion = false">
                <template #title>
                    Delete Location
                </template>

                <template #content>
                    Are you sure you want to delete this location? This action cannot be undone.
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingLocationDeletion = false">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': false }"
                        @click="deleteLocation"
                    >
                        Delete
                    </PrimaryButton>
                </template>
            </ConfirmationModal>
        </Container>
    </AppLayout>
</template>
