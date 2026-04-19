<script setup>
import {
    onMounted,
    ref
} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {
    Link
} from '@inertiajs/vue3';
import { useTheme } from '@/stores/themeStore';
const {theme, toggleTheme}=useTheme();
//import {
//    useThemeStore
//} from '@/stores/themeStore';
//const store = useThemeStore();
const showingNavigationDropdown = ref(false);

</script>   

<template  >
<div :class="{'dark':isDark}" class="transition-colors duration-300">
    <div class="bg-gray-50 dark:bg-gray-900 text-black dark:text-white min-h-screen">
        <nav class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 transition-colors duration-300">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex items-center space-x-6">
                            <Link :href="route('dashboard')"  class="flex items-center">
                            <ApplicationLogo class="h-9 w-auto fill-current text-gray-800 dark:text-white" />
                            </Link>
                        </div>
                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
                            class="relative px-3 py-2 rounded-lg font-medium text-gray-700 dark:text-gray-200
                            hover:bg-blue-50 dark:hover:bg-blue-900/30 
                            transition-all duration-300 group">
                                <span class="relative z-10 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                 Dashboard
                                </span>
                                
                            </NavLink>
                            <NavLink :href="route('tasks.index')" :active="route().current('tasks.index')"
                            class="relative px-3 py-2 rounded-lg font-medium text-gray-700 dark:text-gray-200
                             hover:bg-green-50 dark:hover:bg-green-900/30 
                             transition-all duration-300 group">
                                 <span class="relative z-10 group-hover:text-green-600 dark:group-hover:text-green-400">
                                    Task
                                </span>
                            </NavLink>

                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button @click="toggleTheme"
                        class="px-3 py-2 rounded text-sm bg-gray-100 dark:bg-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        {{ isDark ?'Light Mode':'Dark Mode' }}
                    </button>
                    <div class="hidden sm:ms-6 sm:flex sm:items-center">
                        <!-- Settings Dropdown -->
                        
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>

                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-200 hover:text-gray-700 dark:hover:text-white focus:outline-none transition">
                                            {{ $page.props.auth.user.name }}

                                            <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>

                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown =!showingNavigationDropdown" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 dark:hover:bg-gray-800 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{hidden: showingNavigationDropdown,
                                              'inline-flex':!showingNavigationDropdown,}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{hidden: !showingNavigationDropdown,
                                            'inline-flex':showingNavigationDropdown,}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }" class="sm:hidden dark:text-gray-100 bg-white dark:bg-gray-800">
                <div class="space-y-1 pb-3 pt-2 ">
                    <ResponsiveNavLink class="" :href="route('dashboard')" :active="route().current('dashboard')">
                        Dashboard
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('tasks.index')" :active="route().current('tasks.index')">
                        Task
                    </ResponsiveNavLink>

                </div>

                <!-- Responsive Settings Options -->
                <div class="border-t border-gray-200 dark:border-gray-700 pb-1 pt-4">
                    <div class="px-4">
                        <div class="text-base font-medium text-gray-800 dark:text-gray-200 ">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">
                            Profile
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                            Log Out
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-gray-50 dark:bg-gray-900 border dark:border-gray-800 shadow transition-colors duration-300" v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class=" mx-auto  sm:px-6 lg:px-8 ">
            <slot />
        </main>
    </div>
</div>
</template>
<style>
.nested-enter-active,
.nested-leave-active {
    transition: all 0.3s ease-in-out;
}

.nested-leave-active {
    transition-delay: 0.05s;
}

.nested-enter-from,
.nested-leave-to {
    transform: translateY(30px);
    opacity: 0;
}
* {
  transition: background-color 0.3s ease, 
              color 0.3s ease, 
              border-color 0.3s ease,
              box-shadow 0.3s ease;
}</style>