import { computed, ref } from 'vue';
import { useToast } from './useToast';

// Runs a write request and reports the outcome as a toast. `onSettled` runs after success and failure alike:
// refetching after a failure also discards optimistic local changes (e.g. a drag the server rejected).
export function useMutation({ onSettled }) {
    const { showToast } = useToast();
    const pending = ref(0);

    async function run(request, successMessage) {
        pending.value++;

        try {
            await request();
            showToast(successMessage);

            return true;
        } catch (e) {
            showToast(e.message, 'error');

            return false;
        } finally {
            await onSettled();
            pending.value--;
        }
    }

    return { run, busy: computed(() => pending.value > 0) };
}
