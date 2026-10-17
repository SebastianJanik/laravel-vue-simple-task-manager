<script setup>
import { ref } from 'vue';
import BaseButton from './BaseButton.vue';
import BaseInput from './BaseInput.vue';

defineProps({
    placeholder: { type: String, required: true },
    buttonLabel: { type: String, required: true },
});

const emit = defineEmits(['submit']);

const name = ref('');

defineExpose({
    reset() {
        name.value = '';
    },
});
</script>

<template>
    <form class="flex flex-col sm:flex-row gap-2 mb-4" @submit.prevent="emit('submit', name.trim())">
        <BaseInput
            v-model="name"
            type="text"
            required
            pattern=".*\S.*"
            :placeholder="placeholder"
            :aria-label="placeholder"
            class="flex-1 min-w-0"
        />
        <BaseButton type="submit">{{ buttonLabel }}</BaseButton>
    </form>
</template>
