import { ref } from 'vue';

// Loads data and keeps only the newest result, so a slow earlier request can't overwrite a newer one.
export function useLoader(load, initialValue) {
    const data = ref(initialValue);
    const loading = ref(false);
    const error = ref(null);
    let latest = 0;

    async function refresh() {
        const current = ++latest;
        loading.value = true;

        try {
            const result = await load();

            if (current === latest) {
                data.value = result;
                error.value = null;
            }
        } catch (e) {
            if (current === latest) {
                error.value = e;
            }
        } finally {
            if (current === latest) {
                loading.value = false;
            }
        }
    }

    return { data, loading, error, refresh };
}
