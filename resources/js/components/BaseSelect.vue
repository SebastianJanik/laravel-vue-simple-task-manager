<script setup>
const model = defineModel({ type: String, default: '' });
</script>

<template>
    <select v-model="model" class="field">
        <slot />
    </select>
</template>
