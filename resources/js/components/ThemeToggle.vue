<script setup>
import { t } from '../i18n';
import { useTheme } from '../composables/useTheme';
import BaseButton from './BaseButton.vue';

const { isDark, toggle } = useTheme();
</script>

<template>
    <BaseButton variant="secondary" size="sm" :aria-pressed="isDark" @click="toggle">
        {{ isDark ? t('theme.light') : t('theme.dark') }}
    </BaseButton>
</template>
