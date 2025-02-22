<script setup>
import { computed } from 'vue';

const props = defineProps({
    color: {
        type: String,
        required: true,
        validator: (value) => /^#[0-9A-Fa-f]{6}$/.test(value)
    },
    text: {
        type: String,
        required: false
    }
});

// Function to determine if a color is light or dark
const isLightColor = (hexColor) => {
    const r = parseInt(hexColor.slice(1, 3), 16);
    const g = parseInt(hexColor.slice(3, 5), 16);
    const b = parseInt(hexColor.slice(5, 7), 16);
    const brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    return brightness > 128;
};

// Compute text color based on background color
const textColor = computed(() => {
    return isLightColor(props.color) ? '#000000' : '#FFFFFF';
});
</script>

<template>
    <div
        class="inline-flex items-center px-2.5 py-0.5 text-sm font-medium rounded-full md:mt-2 lg:mt-0"
        :style="{
            backgroundColor: color,
            color: textColor
        }"
    >
        <span class="flex items-center">
            {{ text }}
            <slot/>
        </span>
    </div>
</template>
