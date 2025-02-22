<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import InputError from '@/Components/InputError.vue';
import NeumorphicBadge from '@/Components/NeumorphicBadge.vue';

const props = defineProps({
    modelValue: {
        type: [Array, String, Number],
        default: () => []
    },
    options: {
        type: Array,
        required: true
    },
    label: {
        type: String,
        default: 'name'
    },
    valueProp: {
        type: String,
        default: 'id'
    },
    placeholder: {
        type: String,
        default: 'Select options'
    },
    multiple: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue']);

const searchQuery = ref('');
const isOpen = ref(false);
const selectedItems = ref([]);

const filteredOptions = computed(() => {
    return props.options.filter(option =>
        option[props.label].toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const getSelectedLabel = (value) => {
    const option = props.options.find(opt => opt[props.valueProp] === value);
    return option ? option[props.label] : '';
};

const removeItem = (itemId) => {
    const newValue = Array.isArray(props.modelValue)
        ? props.modelValue.filter(id => id !== itemId)
        : null;
    emit('update:modelValue', newValue);
};

const selectOption = (option) => {
    const value = option[props.valueProp];
    if (props.multiple) {
        const newValue = Array.isArray(props.modelValue)
            ? props.modelValue.includes(value)
                ? props.modelValue.filter(id => id !== value)
                : [...props.modelValue, value]
            : [value];
        emit('update:modelValue', newValue);
    } else {
        emit('update:modelValue', value);
        isOpen.value = false;
    }
    searchQuery.value = '';
};

const handleClickOutside = (event) => {
    if (!event.target.closest('.search-multiselect')) {
        isOpen.value = false;
    }
};

watch(() => props.modelValue, (newValue) => {
    if (Array.isArray(newValue)) {
        selectedItems.value = newValue;
    }
}, { immediate: true });

// Add event listener for click outside
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="relative search-multiselect">
        <!-- Selected Items Display -->
        <div
            v-if="multiple && modelValue?.length > 0"
            class="flex flex-wrap gap-2 p-2 mb-2 min-h-[2.5rem] rounded-md border border-gray-300 dark:border-gray-700"
        >
            <NeumorphicBadge
                v-for="value in modelValue"
                :key="value"
                color="#D3D3D3"
            >
                <button
                    @click.prevent="removeItem(value)"
                    class="inline-flex items-center"
                >
                    {{ getSelectedLabel(value) }}
                    <span class="ml-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>
            </NeumorphicBadge>
        </div>

        <!-- Search Input -->
        <div
            @click="isOpen = true"
            class="relative w-full cursor-text"
        >
            <input
                type="text"
                v-model="searchQuery"
                :placeholder="placeholder"
                class="px-3 py-2 w-full text-base rounded-md border-gray-300 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:border-orange-500 focus:ring-orange-500"
                @focus="isOpen = true"
            >
            <div class="flex absolute inset-y-0 right-0 items-center pr-2">
                <svg
                    class="w-5 h-5 text-gray-400"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
        </div>

        <!-- Dropdown Options -->
        <div
            v-if="isOpen"
            class="overflow-auto absolute z-10 mt-1 w-full max-h-60 bg-white rounded-md shadow-lg dark:bg-gray-800"
        >
            <ul class="py-1">
                <li
                    v-for="option in filteredOptions"
                    :key="option[valueProp]"
                    @click="selectOption(option)"
                    class="px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-between"
                >
                    <span :class="{ 'text-gray-400 dark:text-gray-500': modelValue?.includes(option[valueProp]) }">{{ option[label] }}</span>
                    <svg
                        v-if="modelValue?.includes(option[valueProp])"
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-orange-500"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </li>
                <li
                    v-if="filteredOptions.length === 0"
                    class="px-3 py-2 text-gray-500 dark:text-gray-400"
                >
                    No results found
                </li>
            </ul>
        </div>
    </div>
</template>
