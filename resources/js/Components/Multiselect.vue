<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, Array],
        default: ''
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
    multiple: {
        type: Boolean,
        default: false
    },
    placeholder: {
        type: String,
        default: 'Select option'
    }
});

const emit = defineEmits(['update:modelValue']);

const selectedValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const getOptionLabel = (option) => {
    return option[props.label];
};

const getOptionValue = (option) => {
    return option[props.valueProp];
};
</script>

<template>
    <div class="relative">
        <select
            v-if="!multiple"
            v-model="selectedValue"
            class="block px-3 py-2 w-full text-base rounded-md border-gray-300 focus:border-orange-500 focus:ring-orange-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
        >
            <option value="">{{ placeholder }}</option>
            <option
                v-for="option in options"
                :key="getOptionValue(option)"
                :value="getOptionValue(option)"
            >
                {{ getOptionLabel(option) }}
            </option>
        </select>

        <select
            v-else
            v-model="selectedValue"
            multiple
            class="block px-3 py-2 w-full text-base rounded-md border-gray-300 focus:border-orange-500 focus:ring-orange-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
        >
            <option
                v-for="option in options"
                :key="getOptionValue(option)"
                :value="getOptionValue(option)"
            >
                {{ getOptionLabel(option) }}
            </option>
        </select>
    </div>
</template>
