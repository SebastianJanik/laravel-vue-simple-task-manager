<script setup>
defineProps({
    variant: { type: String, default: 'primary' },
    size: { type: String, default: 'md' },
    type: { type: String, default: 'button' },
});

const variants = {
    primary: 'bg-blue-600 hover:bg-blue-700 text-white',
    secondary: 'border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800',
    danger: 'bg-danger hover:brightness-90 text-white',
    'ghost-danger':
        'text-danger dark:text-danger-dark border border-transparent hover:border-danger/60 dark:hover:border-danger-dark/60 hover:bg-danger/10 dark:hover:bg-danger-dark/10',
};

const sizes = {
    md: 'px-4 py-2',
    sm: 'px-2 py-1 text-sm',
};
</script>

<template>
    <button
        :type="type"
        class="relative rounded cursor-pointer disabled:opacity-50"
        :class="[variants[variant], sizes[size]]"
    >
        <slot />
    </button>
</template>
