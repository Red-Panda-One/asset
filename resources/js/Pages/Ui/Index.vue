<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Table from '@/Components/Table.vue';

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'title', label: 'Title' },
    { key: 'email', label: 'Email' },
    { key: 'role', label: 'Role' }
];

const items = [
    {
        id: 1,
        name: 'John Doe',
        title: 'Senior Developer',
        email: 'john@example.com',
        role: 'Admin'
    },
    {
        id: 2,
        name: 'Jane Smith',
        title: 'Project Manager',
        email: 'jane@example.com',
        role: 'Manager'
    },
    {
        id: 3,
        name: 'Bob Wilson',
        title: 'Developer',
        email: 'bob@example.com',
        role: 'Staff'
    }
];

const handleAdd = () => {
    console.log('Add new user');
};

const handleEdit = (item) => {
    console.log('Edit user:', item);
};

const handleBulkEdit = (selectedItems) => {
    console.log('Bulk edit users:', selectedItems);
};

const handleBulkDelete = (selectedItems) => {
    console.log('Bulk delete users:', selectedItems);
};
</script>

<template>
    <AppLayout title="Users">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Users Table Demo
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Table
                    title="Users"
                    description="A list of all users in the system"
                    :columns="columns"
                    :items="items"
                    :selectable="true"
                    @cta="handleAdd"
                    @edit="handleEdit"
                    @bulk-edit="handleBulkEdit"
                    @bulk-delete="handleBulkDelete"
                />
            </div>
        </div>
    </AppLayout>
</template>
