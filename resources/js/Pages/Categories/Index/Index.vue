<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue'
import Table from '@/Components/Table.vue'
import { router } from '@inertiajs/vue3';
import NeumorphicBadge from '@/Components/NeumorphicBadge.vue';
import { h } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ref } from 'vue';

interface Category {
    id: string;
    name: string;
    description: string;
    color: string;
}

interface Props {
    categories: {
        data: Category[];
    };
}

const props = defineProps<Props>();

const columns = [
    { key: 'name', label: 'Name', render: (item: Category) => (
        h(NeumorphicBadge, {
            color: item.color || '#646a75',
            text: item.name
        })
    ) },
    { key: 'description', label: 'Description' },
];

const confirmingCategoryDeletion = ref<boolean>(false);
const categoryToDelete = ref<Category | null>(null);

const handleAdd = (): void => {
    router.get(route('categories.create'));
}

const handleEdit = (category: Category): void => {
    router.get(route('categories.edit', category.id));
};

const confirmCategoryDeletion = (category: Category): void => {
    categoryToDelete.value = category;
    confirmingCategoryDeletion.value = true;
};

const deleteCategory = (): void => {
    if (!categoryToDelete.value) return;

    router.delete(route('categories.destroy', categoryToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingCategoryDeletion.value = false;
            categoryToDelete.value = null;
        },
    });
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
                @cta="handleAdd"
                @edit="handleEdit"
                @delete="confirmCategoryDeletion"
            />

            <!-- Delete Confirmation Modal -->
            <ConfirmationModal :show="confirmingCategoryDeletion" @close="confirmingCategoryDeletion = false">
                <template #title>
                    Delete Category
                </template>

                <template #content>
                    Are you sure you want to delete this category? This action cannot be undone.
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingCategoryDeletion = false">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': false }"
                        @click="deleteCategory"
                    >
                        Delete
                    </PrimaryButton>
                </template>
            </ConfirmationModal>
        </Container>
    </AppLayout>
</template>
