<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { HomeIcon, CubeIcon, ChevronDoubleLeftIcon, ChevronDoubleRightIcon, ClipboardDocumentListIcon } from "@heroicons/vue/24/outline";


const sidebarOpen = ref(true);

const navigationItems = [
    { name: 'Dashboard', route: '/dashboard', icon: HomeIcon },
    { name: 'Products', route: '/products', icon: CubeIcon },
    { name: 'Orders', route: '/orders', icon: ClipboardDocumentListIcon },
];

const isActiveRoute = (route) => {
    return window.location.pathname === route;
};
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Left Sidebar Navigation -->
        <aside 
            :class="[
                'bg-gray-800 text-white transition-all duration-300 flex flex-col',
                sidebarOpen ? 'w-64' : 'w-20'
            ]"
        >
            <!-- Sidebar Header -->
            <div :class="['flex items-center p-4 border-b border-gray-700', sidebarOpen ? 'justify-between' : 'justify-center']">
                <Link v-if="sidebarOpen" :href="route('dashboard')" class="flex items-center space-x-3">
                    <span class="text-xl font-semibold">Laravel Ecomm</span>
                </Link>
                <button 
                    @click="sidebarOpen = !sidebarOpen"
                    class="text-gray-400 hover:text-white focus:outline-none"
                    aria-label="Toggle sidebar"
                >
                    <ChevronDoubleLeftIcon v-if="sidebarOpen" class="w-6 h-6" />
                    <ChevronDoubleRightIcon v-else class="w-6 h-6" />
                </button>
            </div>

            <!-- Navigation Items -->
            <nav class="flex-1 overflow-y-auto py-4 px-3">
                <Link
                    v-for="item in navigationItems"
                    :key="item.route"
                    :href="item.route"
                    :class="[
                        'flex items-center px-3 py-3 mb-2 rounded-lg transition-all duration-200',
                        'border border-transparent',
                        isActiveRoute(item.route)
                            ? 'bg-gray-900 border-indigo-500 text-white shadow-lg'
                            : 'text-gray-300 hover:bg-gray-700 hover:border-gray-600 hover:text-white'
                    ]"
                >
                    <!-- Icon -->
                    <component :is="item.icon" class="w-6 h-6 flex-shrink-0" />
                    
                    <!-- Label -->
                    <span 
                        v-show="sidebarOpen" 
                        class="ml-3 text-sm font-medium"
                    >
                        {{ item.name }}
                    </span>
                </Link>
            </nav>

            <!-- Sidebar Footer - User Info -->
            <div class="border-t border-gray-700 p-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-semibold">
                            {{ $page.props.auth.user.name.charAt(0) }}
                        </span>
                    </div>
                    <div v-show="sidebarOpen" class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">
                            {{ $page.props.auth.user.name }}
                        </p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ $page.props.auth.user.email }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header Bar -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Page Title from Slot -->
                    <div v-if="$slots.header">
                        <slot name="header" />
                    </div>
                    <h1 v-else class="text-2xl font-semibold text-gray-800">
                    </h1>

                    <!-- Header Actions -->
                    <div class="flex items-center space-x-4">

                        <!-- User Menu -->
                        <div class="relative">
                            <Link
                                :href="route('profile.edit')"
                                class="text-gray-700 hover:text-gray-900 text-sm font-medium"
                            >
                                Profile
                            </Link>
                        </div>

                        <!-- Logout -->
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-gray-700 hover:text-gray-900 text-sm font-medium"
                        >
                            Logout
                        </Link>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-100 p-6">
                <slot />
            </main>

            <!-- Bottom Footer Bar -->
            <footer class="bg-white border-t border-gray-200 py-3 px-6">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div>
                        <span>&copy; 2026 Andrei Duicu Laravel Ecomm Example. All rights reserved.</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>
                    