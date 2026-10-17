<script setup>
import ThemeToggle from './ThemeToggle.vue';
</script>

<template>
    <div class="max-w-xl mx-auto p-4 sm:p-6">
        <div class="flex justify-end mb-4">
            <ThemeToggle />
        </div>
        <main>
            <slot />
        </main>
    </div>
</template>
