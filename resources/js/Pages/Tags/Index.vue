<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Table from '@/Components/Table.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import NeumorphicBadge from '@/Components/NeumorphicBadge.vue';
import { h } from 'vue';


const props = defineProps({
    tags: {
        type: Object,
        required: true,
    }
});

const columns = [
    { key: 'name', label: 'Name', render: (item) => (
        h(NeumorphicBadge, {
            color: '#F2F4F7',
            text: item.name
        })
    ) },
    { key: 'description', label: 'Description' },
];

const handleAdd = () => {
    router.get(route('tags.create'));
};

const handleEdit = (tag) => {
    router.get(route('tags.edit', tag.id));
};

const handleDelete = (tag) => {
    console.log('Deleting tag:', tag); // Debug log
    console.log('Tag ID', tag.id);

    if (confirm('Are you sure you want to delete this tag?')) {
        router.delete(route('tags.destroy', tag), {
            preserveScroll: true,
            onSuccess: () => {
                // Flash message is handled by the controller
            },
        });
    }
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
                    :show-checkboxes="false"
                    :columns="columns"
                    :items="tags.data"
                    :selectable="true"
                    @add="handleAdd"
                    @edit="handleEdit"
                    @delete="handleDelete"
                />
            </div>
        </div>
    </AppLayout>
</template>
