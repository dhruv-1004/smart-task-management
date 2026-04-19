import { ref, onMounted, watch } from "vue";

const isDark = ref(false);

export function useTheme() {
    // Initialize from localStorage on mount
    onMounted(() => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            isDark.value = true;
        } else {
            isDark.value = false;
        }
        applyTheme();
    });

    // Apply theme by toggling dark class
    const applyTheme = () => {
        const html = document.documentElement;
        if (isDark.value) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    };

    // Toggle function
    const toggleTheme = () => {
        isDark.value = !isDark.value;
        localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
        applyTheme();
    };

    watch(isDark, applyTheme);

    return { isDark, toggleTheme };
}