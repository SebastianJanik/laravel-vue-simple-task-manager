<script setup>
import { ref, watch } from 'vue';
import { t } from '../i18n';
import BaseInput from './BaseInput.vue';
import ItemRow from './ItemRow.vue';

const props = defineProps({
    task: { type: Object, required: true },
    dragDisabled: { type: Boolean, default: false },
});

const emit = defineEmits(['rename', 'delete']);

const draft = ref(props.task.name);

// Every refetch replaces the task object, which also discards a rename the server rejected.
watch(
    () => props.task,
    (task) => {
        draft.value = task.name;
    },
);

function commitRename() {
    const name = draft.value.trim();

    if (name && name !== props.task.name) {
        emit('rename', name);
    } else {
        draft.value = props.task.name;
    }
}
</script>

<template>
    <ItemRow :name="task.name" @delete="emit('delete')">
        <span
            class="drag-handle text-muted dark:text-muted-dark min-w-10 shrink-0 touch-none select-none"
            :class="dragDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-grab'"
            :title="t('tasks.dragToReorder')"
        >
            <span aria-hidden="true">⠿</span>
            <span :aria-label="t('tasks.priorityAria', { priority: task.priority })">{{ task.priority }}</span>
        </span>
        <BaseInput
            v-model="draft"
            bare
            :aria-label="t('tasks.nameAria')"
            class="flex-1 min-w-0"
            @change="commitRename"
        />
    </ItemRow>
</template>
