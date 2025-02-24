<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Table from '@/Components/Table.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import NeumorphicBadge from '@/Components/NeumorphicBadge.vue';
import Container from '@/Components/Container.vue'
import { h } from 'vue';

interface Tag {
    id: string;
    name: string;
    description: string;
}

interface Column {
    key: keyof Tag;
    label: string;
    render?: (item: Tag) => any;
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

interface TagsData {
    data: Tag[];
    meta: PaginationMeta;
}

const props = defineProps<{
    tags: TagsData;
    filters: Filters;
}>();

const columns: Column[] = [
    { key: 'name', label: 'Name', render: (item: Tag) => (
        h(NeumorphicBadge, {
            color: '#F2F4F7',
            text: item.name
        })
    ) },
    { key: 'description', label: 'Description' },
];

const confirmingTagDeletion = ref(false);
const tagToDelete = ref<Tag | null>(null);

const handleAdd = () => {
    router.visit(route('tags.create'));
};

const handleEdit = (tag: Tag) => {
    router.visit(route('tags.edit', tag.id));
};

const confirmTagDeletion = (tag: Tag) => {
    tagToDelete.value = tag;
    confirmingTagDeletion.value = true;
};

const deleteTag = () => {
    if (!tagToDelete.value) return;

    router.delete(route('tags.destroy', tagToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingTagDeletion.value = false;
            tagToDelete.value = null;
        },
    });
};
</script>

<template>
    <AppLayout title="Tags">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tags
            </h2>
        </template>
       <Container>
                <Table
                    title="Tags"
                    description="A list of all tags in the system"
                    :show-checkboxes="false"
                    :columns="columns"
                    :items="tags.data"
                    :selectable="true"
                    @cta="handleAdd"
                    @edit="handleEdit"
                    @delete="confirmTagDeletion"
                />

                <!-- Delete Confirmation Modal -->
            <ConfirmationModal :show="confirmingTagDeletion" @close="confirmingTagDeletion = false">
                <template #title>
                    Delete Tag
                </template>

                <template #content>
                    Are you sure you want to delete this tag? This action cannot be undone.
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingTagDeletion = false">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': false }"
                        @click="deleteTag"
                    >
                        Delete
                    </PrimaryButton>
                </template>
            </ConfirmationModal>
        </Container>
    </AppLayout>
</template>
