<script setup>
import { t } from '../i18n';
import BaseButton from './BaseButton.vue';

defineProps({
    name: { type: String, required: true },
    clickable: { type: Boolean, default: false },
});

defineEmits(['delete']);
</script>

<template>
    <li
        class="card relative flex items-center gap-2 px-3 py-2 in-[ul:not(.dragging)]:hover:bg-gray-100 dark:in-[ul:not(.dragging)]:hover:bg-gray-800"
        :class="{ 'cursor-pointer': clickable }"
    >
        <slot />
        <BaseButton
            variant="ghost-danger"
            size="sm"
            class="shrink-0"
            :aria-label="t('common.deleteAria', { name })"
            @click="$emit('delete')"
        >
            {{ t('common.delete') }}
        </BaseButton>
    </li>
</template>
