<script setup>
import { ref, useId, watch } from 'vue';
import { t } from '../i18n';
import BaseButton from './BaseButton.vue';

const props = defineProps({
    item: { type: Object, default: null },
    title: { type: String, required: true },
});

const emit = defineEmits(['confirm', 'cancel']);

const dialog = ref(null);
const titleId = useId();

watch(
    () => props.item,
    (item) => {
        if (item && !dialog.value.open) {
            dialog.value.showModal();
        } else if (!item && dialog.value.open) {
            dialog.value.close();
        }
    },
);
</script>

<template>
    <dialog
        ref="dialog"
        :aria-labelledby="titleId"
        class="card m-auto p-0 w-[90vw] max-w-sm text-inherit backdrop:bg-black/50"
        @cancel.prevent="emit('cancel')"
        @click.self="emit('cancel')"
    >
        <div class="p-5">
            <h2 :id="titleId" class="text-lg font-semibold">{{ title }}</h2>
            <p class="mt-2 text-sm text-muted dark:text-muted-dark break-words">
                {{ t('deleteConfirmation.body', { name: item?.name }) }}
            </p>
            <div class="mt-5 flex justify-end gap-2">
                <BaseButton variant="secondary" @click="emit('cancel')">{{ t('common.cancel') }}</BaseButton>
                <BaseButton variant="danger" @click="emit('confirm')">{{ t('common.delete') }}</BaseButton>
            </div>
        </div>
    </dialog>
</template>
