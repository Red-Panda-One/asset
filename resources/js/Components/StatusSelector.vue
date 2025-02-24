<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { baseStatusOptions, type Status, type StatusOption } from '@/types/status';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

interface Props {
    modelValue: Status;
    label?: string;
    error?: string;
    teamMembers?: Array<{ id: number; name: string }>;
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Status',
    teamMembers: () => []
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: Status): void;
}>();

const isCustom = ref(false);
const customName = ref('');

const allOptions = computed(() => {
    const options = [...baseStatusOptions];
    if (isCustom.value && customName.value) {
        options.push({
            value: `Assigned to ${customName.value}` as Status,
            label: `Assigned to ${customName.value}`,
            type: 'assigned'
        });
    }
    return options;
});

const selectedStatus = computed({
    get() {
        return props.modelValue;
    },
    set(value: Status) {
        emit('update:modelValue', value);
    }
});

const handleCustomAssignment = () => {
    if (isCustom.value && customName.value) {
        selectedStatus.value = `Assigned to ${customName.value}` as Status;
    } else {
        selectedStatus.value = 'Available' as Status;
    }
};

watch(isCustom, (newValue) => {
    if (!newValue) {
        customName.value = '';
        selectedStatus.value = 'Available' as Status;
    }
});

const handleOptionSelect = () => {
    isCustom.value = false;
    customName.value = '';
};

</script>

<template>
    <div>
        <InputLabel :for="label" :value="label" />
        <div class="mt-1 space-y-2">
            <select
                :id="label"
                v-model="selectedStatus"
                @change="handleOptionSelect"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
            >
                <option v-for="option in allOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>

            <div class="flex items-center space-x-2">
                <input
                    type="checkbox"
                    v-model="isCustom"
                    class="text-orange-600 rounded border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50"
                />
                <span class="text-sm text-gray-700 dark:text-gray-300">Assign to someone</span>
            </div>

            <div v-if="isCustom" class="flex space-x-2">
                <input
                    type="text"
                    v-model="customName"
                    placeholder="Enter name"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                    @change="handleCustomAssignment"
                />
            </div>
        </div>
        <InputError :message="error" class="mt-2" />
    </div>
</template>
