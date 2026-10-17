import js from '@eslint/js';
import prettier from 'eslint-config-prettier';
import pluginVue from 'eslint-plugin-vue';
import globals from 'globals';

export default [
    { ignores: ['public/**', 'vendor/**', 'node_modules/**', 'storage/**'] },
    js.configs.recommended,
    ...pluginVue.configs['flat/recommended'],
    prettier,
    {
        languageOptions: { globals: globals.browser },
        rules: { 'vue/multi-word-component-names': 'off' },
    },
];
