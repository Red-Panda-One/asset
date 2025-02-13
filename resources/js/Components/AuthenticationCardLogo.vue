<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const isDark = ref(false);

const checkDarkMode = () => {
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
};

onMounted(() => {
    checkDarkMode();
    // Watch for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)')
        .addEventListener('change', checkDarkMode);
});
</script>

<template>
    <Link :href="'/'">
        <img
        v-if="isDark"
        src="../../svg/logo.svg"
        alt="Red Panda One Logo"
        class="h-12"
    />
    <img
        v-else
        src="../../svg/logo-lightMode.svg"
        alt="Red Panda One Logo"
        class="h-12"
    />
    </Link>
</template>
