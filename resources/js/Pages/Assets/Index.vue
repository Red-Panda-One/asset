<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="Assets">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Assets
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Assets content will go here -->
                    <div class="text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium">Asset Management</h3>
                        <p class="mt-2">Welcome to the asset management section.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>