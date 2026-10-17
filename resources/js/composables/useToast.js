import { readonly, ref } from 'vue';

const DISPLAY_MS = { success: 2000, error: 5000 };

// Module-level state: one toast for the whole app, so a new message replaces the previous one.
const toast = ref(null);
let nextId = 0;
let hideTimer = null;

function showToast(message, type = 'success') {
    clearTimeout(hideTimer);
    toast.value = { id: ++nextId, message, type };
    hideTimer = setTimeout(() => {
        toast.value = null;
    }, DISPLAY_MS[type]);
}

export function useToast() {
    return { toast: readonly(toast), showToast };
}
