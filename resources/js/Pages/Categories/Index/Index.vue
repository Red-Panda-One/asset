<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '../../../Components/Container.vue'
import Table from '../../../Components/Table.vue'
import { router } from '@inertiajs/vue3';
import NeumorphicBadge from '@/Components/NeumorphicBadge.vue';
import { h } from 'vue';

const props = defineProps({
    categories: {
        type: Object,
        required: true,
    }
});

const columns = [
    { key: 'name', label: 'Name', render: (item) => (
        h(NeumorphicBadge, {
            color: item.color || '#646a75',
            text: item.name
        })
    ) },
    { key: 'description', label: 'Description' },
];

const handleAdd = () => {
    router.get(route('categories.create'));
}

const handleEdit = (category) => {
    console.log('Editing category:', category); // Debug log

};

const handleDelete = (category) => {
    console.log('Deleting category:', category); // Debug log
};

</script>

<template>
    <AppLayout title="Categories">
            <template #header>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Categories
                </h2>
            </template>
            <Container>
                <Table
                    title="Categories"
                    description="A list of all categories in the system"
                    :show-checkboxes="false"
                    :columns="columns"
                    :items="categories.data"
                    :selectable="true"
                    @add="handleAdd"
                    @edit="handleEdit"
                    @delete="handleDelete"
                />
        </Container>
    </AppLayout>

</template>
