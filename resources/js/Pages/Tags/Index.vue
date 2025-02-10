<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Table from '@/Components/Table.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

const props = defineProps({
    tags: {
        type: Object,
        required: true,
    }
});

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'description', label: 'Description' },
];

const handleAdd = () => {
    router.get(route('tags.create'));
};

const handleEdit = (tag) => {
    router.get(route('tags.edit', tag.id));
};

const handleBulkDelete = (selected) => {
    // Implement bulk delete logic
};
</script>

<template>
    <AppLayout title="Tags">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tags
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Table
                    title="Tags"
                    description="A list of all tags in the system"
                    :columns="columns"
                    :items="tags.data"
                    :selectable="true"
                    @add="handleAdd"
                    @edit="handleEdit"
                    @bulk-delete="handleBulkDelete"
                />
            </div>
        </div>
    </AppLayout>
</template>
