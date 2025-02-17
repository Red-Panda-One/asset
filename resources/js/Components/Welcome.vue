<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div>
        <div class="p-6 bg-white border-b border-gray-200 lg:p-8 dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent dark:border-gray-700">
            <ApplicationLogo class="block w-auto h-12" />

            <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
                Welcome to Asset RPO!
            </h1>

            <p class="mt-6 leading-relaxed text-gray-500 dark:text-gray-400">
                Dashboard under construction.
            </p>
        </div>


        <div class="grid grid-cols-1 gap-6 p-6 bg-gray-200 bg-opacity-25 dark:bg-gray-800 md:grid-cols-2 lg:gap-8 lg:p-8">
            <div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900 ms-3 dark:text-white">
                        <a href="https://#">Documentation</a>
                    </h2>
                </div>

                <p class="mt-4 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                    Coming Soon
                </p>

                <p class="mt-4 text-sm">
                    <a href="https://#" class="inline-flex items-center font-semibold text-orange-700 dark:text-orange-300">
                        Explore the documentation

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-orange-500 dark:fill-orange-200">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>
            <div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900 ms-3 dark:text-white">
                        <a href="https://#">Version system - Seedling 0.0.0</a>
                    </h2>
                </div>

                <div class="pt-6 space-y-6">
                    <div class="p-4 rounded-lg bg-white/50 dark:bg-gray-700/50">
                        <h3 class="text-lg font-semibold text-orange-600 dark:text-orange-400">Seedling - Alpha MVP</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                            The first experimental version. Like a seedling just breaking through the soil, this MVP is fragile and developing. Intended for internal testing and select group feedback.
                        </p>
                    </div>

                    <div class="p-4 rounded-lg bg-white/50 dark:bg-gray-700/50">
                        <h3 class="text-lg font-semibold text-orange-600 dark:text-orange-400">Shoot - Beta MVP</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                            Growing stronger from the seedling phase. More feature-rich and stable, ready for beta testing and wider audience feedback.
                        </p>
                    </div>

                    <div class="p-4 rounded-lg bg-white/50 dark:bg-gray-700/50">
                        <h3 class="text-lg font-semibold text-orange-600 dark:text-orange-400">Bamboo - Official Release</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                            The mature, stable release. Like bamboo, it's strong and resilient, ready for the market and general public use.
                        </p>
                    </div>

                    <div class="p-4 rounded-lg bg-white/50 dark:bg-gray-700/50">
                        <h3 class="text-lg font-semibold text-orange-600 dark:text-orange-400">Bamboo 1, 2, etc.</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                            Continuous growth and improvements. Each version brings new features, bug fixes, and enhancements, just as bamboo grows stronger over time.
                        </p>
                    </div>
                </div>
            </div>



        </div>

    </div>
</template>
