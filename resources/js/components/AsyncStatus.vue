<script setup>
import { t } from '../i18n';

defineProps({
    loading: { type: Boolean, default: false },
    error: { type: String, default: null },
    empty: { type: Boolean, default: false },
    emptyMessage: { type: String, default: '' },
});
</script>

<template>
    <p v-if="loading">{{ t('common.loading') }}</p>
    <p v-else-if="error" role="alert" class="text-danger dark:text-danger-dark">{{ error }}</p>
    <p v-else-if="empty" class="text-muted dark:text-muted-dark">{{ emptyMessage }}</p>
</template>
