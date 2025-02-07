<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="Tags">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tags
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Assets content will go here -->
                    <div class="text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium">Tag Management</h3>
                        <p class="mt-2">Welcome to the tag management section.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>