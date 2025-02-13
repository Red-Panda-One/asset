<script setup>
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

const confirmingTagDeletion = ref(false);
const tagToDelete = ref(null);

const handleAdd = () => {
    router.get(route('tags.create'));
};

const handleEdit = (tag) => {
    router.get(route('tags.edit', tag.id));
};

const confirmTagDeletion = (tag) => {
    tagToDelete.value = tag;
    confirmingTagDeletion.value = true;
};

const deleteTag = (tag) => {
    console.log('Deleting tag:', tag); // Debug log
    console.log('Tag ID', tag.id);


    router.delete(route('tags.destroy', tagToDelete.value), {
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
                    @add="handleAdd"
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
