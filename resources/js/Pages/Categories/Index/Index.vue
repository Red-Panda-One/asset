<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '../../../Components/Container.vue'
</script>

<template>
    <AppLayout title="Categories">
            <template #header>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Categories
                </h2>
            </template>
            <Container>
                <h1 class="text-gray-800 dark:text-gray-200">
                Categories Page
                </h1>
        </Container>
    </AppLayout>

</template>
