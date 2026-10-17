<script setup>
import { useToast } from '../composables/useToast';

const { toast } = useToast();
</script>

<template>
    <div class="fixed top-4 right-4 flex justify-end pointer-events-none" role="status" aria-live="polite">
        <Transition name="toast" mode="out-in">
            <div
                v-if="toast"
                :key="toast.id"
                class="px-4 py-2 rounded shadow-lg text-white"
                :class="toast.type === 'error' ? 'bg-danger' : 'bg-green-600'"
            >
                {{ toast.message }}
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition:
        opacity 0.15s,
        transform 0.15s;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
