import { t } from './i18n';

export class ApiError extends Error {
    constructor(message, status) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
    }
}

export async function apiFetch(path, options = {}) {
    const response = await fetch(`/api${path}`, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            ...options.headers,
        },
    });

    if (!response.ok) {
        const body = await response.json().catch(() => null);

        throw new ApiError(body?.message ?? t('common.requestFailed', { status: response.status }), response.status);
    }

    if (response.status === 204) {
        return null;
    }

    return response.json();
}

export function sendJson(method, path, payload) {
    return apiFetch(path, { method, body: JSON.stringify(payload) });
}
