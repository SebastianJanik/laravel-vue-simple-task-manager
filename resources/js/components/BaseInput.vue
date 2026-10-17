<script setup>
defineProps({
    // Borderless variant for text that is edited in place.
    bare: { type: Boolean, default: false },
});

const model = defineModel({ type: String, default: '' });
</script>

<template>
    <input v-model="model" :class="bare ? 'bare-field' : 'field'" />
</template>
