import en from './en';

const messages = { en };
const locale = document.documentElement.lang in messages ? document.documentElement.lang : 'en';

export function t(key, params = {}) {
    const message = key.split('.').reduce((node, part) => node?.[part], messages[locale]) ?? key;

    return message.replace(/\{(\w+)\}/g, (placeholder, name) => params[name] ?? placeholder);
}
