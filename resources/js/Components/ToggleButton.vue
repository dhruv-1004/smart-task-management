<script setup>
import { useThemeStore } from '@/stores/themeStore';
const store=useThemeStore();
const theme=store.theme;
const toggleTheme=store.toggleTheme;
</script>
<template>
    <button @click="toggleTheme" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-900 darK:text-gray-100 transition-colors">
        {{ theme === 'light' ? '🌙 Dark Mode' : '🌞 Light Mode' }}
    </button>
</template>