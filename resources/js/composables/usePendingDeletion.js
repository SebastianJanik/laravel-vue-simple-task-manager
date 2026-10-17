import { ref } from 'vue';

// Holds the item awaiting a delete confirmation; `remove` runs only after the user confirms.
export function usePendingDeletion(remove) {
    const pending = ref(null);

    function ask(item) {
        pending.value = item;
    }

    function cancel() {
        pending.value = null;
    }

    async function confirm() {
        const item = pending.value;
        pending.value = null;
        await remove(item);
    }

    return { pending, ask, cancel, confirm };
}
