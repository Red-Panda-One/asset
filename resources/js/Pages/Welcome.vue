<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useVersion } from '@/Composables/useVersion';

const { version } = useVersion();

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-lime-50 text-emerald-950 dark:bg-emerald-950 dark:text-lime-100">
        <div class="flex relative flex-col items-center min-h-screen">
            <div class="relative px-6 w-full max-w-2xl lg:max-w-7xl">
                <header class="grid grid-cols-2 gap-2 items-center py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        <img
                            src="@/../../resources/svg/logo.svg"
                            alt="Logo"
                            class="hidden w-auto h-12 dark:block"
                        />
                        <img
                            src="@/../../resources/svg/logo-lightMode.svg"
                            alt="Logo"
                            class="block w-auto h-12 dark:hidden"
                        />
                    </div>
                    <nav v-if="canLogin" class="flex flex-1 justify-end -mx-3">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="px-3 py-2 rounded-md ring-1 ring-transparent transition text-emerald-950 hover:text-orange-500 focus:outline-none focus-visible:ring-orange-500 dark:text-lime-100 dark:hover:text-orange-500"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="px-3 py-2 rounded-md ring-1 ring-transparent transition text-emerald-950 hover:text-orange-500 focus:outline-none focus-visible:ring-orange-500 dark:text-lime-100 dark:hover:text-orange-500"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="px-3 py-2 rounded-md ring-1 ring-transparent transition text-emerald-950 hover:text-orange-500 focus:outline-none focus-visible:ring-orange-500 dark:text-lime-100 dark:hover:text-orange-500"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="flex flex-col items-center mt-6">
                    <h1 class="mb-8 text-5xl text-emerald-950 dark:text-lime-100">Asset Management Made <span class="font-bold text-orange-500">Simple</span></h1>
                    <p class="mb-12 max-w-2xl text-xl text-center text-emerald-950/80 dark:text-lime-100/80">
                        Streamline your asset tracking and management with our intuitive platform.
                    </p>
                    <div class="flex justify-evenly">

                        <div class="z-10 mt-12 w-full max-w-4xl">
                            <img
                                src="@/../../resources/img/assetPage.png"
                                alt="Asset Management Dashboard"
                                class="w-full h-auto rounded-3xl border-4 border-orange-500 shadow-2xl -10"
                            />
                        </div>
                    </div>
                </main>
                <footer class="py-8 mt-20 text-sm text-center text-emerald-950/70 dark:text-lime-100/70">
                    <p>Asset RPO by Red Panda One © 2025</p>
                    <p class="mt-1">Seedling {{ version }}</p>
                </footer>
            </div>
        </div>
    </div>
</template>
