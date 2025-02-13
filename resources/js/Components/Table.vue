<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: false,
        default: ''
    },
    description: {
        type: String,
        required: false,
        default: ''
    },
    columns: {
        type: Array,
        required: true
    },
    items: {
        type: Array,
        required: true
    },
    selectable: {
        type: Boolean,
        default: false
    },
    showCheckboxes: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['add', 'edit', 'delete', 'bulk-edit', 'bulk-delete']);

const selectedItems = ref([]);
const sortColumn = ref('');
const sortDirection = ref('asc');

const sortedItems = computed(() => {
    if (!sortColumn.value) return props.items;

    return [...props.items].sort((a, b) => {
        const aValue = a[sortColumn.value];
        const bValue = b[sortColumn.value];

        if (sortDirection.value === 'asc') {
            return aValue > bValue ? 1 : -1;
        }
        return aValue < bValue ? 1 : -1;
    });
});

const toggleSort = (column) => {
    if (sortColumn.value === column.key) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column.key;
        sortDirection.value = 'asc';
    }
};

const toggleSelectAll = () => {
    if (selectedItems.value.length === props.items.length) {
        selectedItems.value = [];
    } else {
        selectedItems.value = props.items.map(item => item.id);
    }
};

const toggleSelect = (item) => {
    const index = selectedItems.value.indexOf(item.id);
    if (index === -1) {
        selectedItems.value.push(item.id);
    } else {
        selectedItems.value.splice(index, 1);
    }
};

const isSelected = (item) => selectedItems.value.includes(item.id);
</script>

<template>
    <div class="bg-white shadow dark:bg-gray-800 sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <!-- Header Section -->
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100" v-if="title">
                        {{ title }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300" v-if="description">
                        {{ description }}
                    </p>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <button
                        type="button"
                        @click="$emit('add')"
                        class="block px-3 py-2 text-sm font-semibold text-center text-white bg-orange-600 rounded-md shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600"
                    >
                        Add
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="flow-root mt-8">
                <div class="overflow-x-auto -mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                    <div class="inline-block py-2 min-w-full align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden ring-1 ring-black ring-opacity-5 shadow sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th v-if="selectable && showCheckboxes" scope="col" class="relative px-7 sm:w-12 sm:px-6">
                                            <input
                                                type="checkbox"
                                                class="absolute left-4 top-1/2 -mt-2 w-4 h-4 text-orange-600 rounded border-gray-300 focus:ring-orange-600"
                                                :checked="selectedItems.length === items.length"
                                                @change="toggleSelectAll"
                                            />
                                        </th>
                                        <th
                                            v-for="column in columns"
                                            :key="column.key"
                                            scope="col"
                                            class="px-3 py-3.5 text-sm font-semibold text-left text-gray-900 cursor-pointer dark:text-gray-100"
                                            @click="toggleSort(column)"
                                        >
                                            <div class="inline-flex group">
                                                {{ column.label }}
                                                <span
                                                    class="flex-none ml-2 text-gray-400 rounded group-hover:visible group-focus:visible"
                                                    :class="{
                                                        'visible': sortColumn === column.key
                                                    }"
                                                >
                                                    <template v-if="sortColumn === column.key">
                                                        <svg v-if="sortDirection === 'asc'" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                                        </svg>
                                                        <svg v-else class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                                                        </svg>
                                                    </template>
                                                    <svg v-else class="invisible w-5 h-5 group-hover:visible" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </th>
                                        <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-6">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-600 dark:bg-gray-800">
                                    <tr v-for="item in sortedItems" :key="item.id" :class="{ 'bg-gray-50 dark:bg-gray-700': isSelected(item) }">
                                        <td v-if="selectable && showCheckboxes" class="relative px-7 sm:w-12 sm:px-6">
                                            <input
                                                type="checkbox"
                                                class="absolute left-4 top-1/2 -mt-2 w-4 h-4 text-orange-600 rounded border-gray-300 focus:ring-orange-600"
                                                :checked="isSelected(item)"
                                                @change="toggleSelect(item)"
                                            />
                                        </td>
                                        <td
                                            v-for="column in columns"
                                            :key="column.key"
                                            class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400"
                                        >
                                            <slot
                                                :name="`cell-${column.key}`"
                                                :item="item"
                                                :value="item[column.key]"
                                                :column="column"
                                            >
                                                <template v-if="column.render">
                                                    <component :is="column.render(item)" />
                                                </template>
                                                <template v-else>
                                                    {{ item[column.key] }}
                                                </template>
                                            </slot>
                                        </td>
                                        <td class="relative py-4 pr-4 pl-3 text-sm font-medium text-right whitespace-nowrap sm:pr-6">
                                            <button
                                                @click="$emit('edit', item)"
                                                class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="$emit('delete', item)"
                                                class="pl-4 text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div v-if="selectable && selectedItems.length > 0" class="flex justify-end items-center mt-4 space-x-3">
                <button
                    @click="$emit('bulk-delete', selectedItems)"
                    class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-red-600 rounded-md shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
                >
                    Delete Selected
                </button>
            </div>
        </div>
    </div>
</template>
