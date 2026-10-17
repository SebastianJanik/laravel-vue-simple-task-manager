import { ref } from 'vue';

// The initial theme is applied by the inline script in app.blade.php before first paint, to avoid a flash.
const isDark = ref(document.documentElement.classList.contains('dark'));

function rememberTheme(theme) {
    try {
        localStorage.setItem('theme', theme);
    } catch {
        // Storage can be blocked (private mode); the choice then lasts only for this page view.
    }
}

export function useTheme() {
    function toggle() {
        isDark.value = !isDark.value;
        document.documentElement.classList.toggle('dark', isDark.value);
        rememberTheme(isDark.value ? 'dark' : 'light');
    }

    return { isDark, toggle };
}
