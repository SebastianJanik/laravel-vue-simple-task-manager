import { watch } from 'vue';
import { apiFetch, sendJson } from '../api';
import { t } from '../i18n';
import { useLoader } from './useLoader';
import { useMutation } from './useMutation';

export function useTasks(projectId) {
    const tasksPath = () => `/projects/${projectId.value}/tasks`;

    const { data: tasks, loading, error, refresh } = useLoader(async () => (await apiFetch(tasksPath())).data, []);
    const { run, busy } = useMutation({ onSettled: refresh });

    watch(projectId, refresh, { immediate: true });

    return {
        tasks,
        loading,
        error,
        busy,
        createTask: (name) => run(() => sendJson('POST', tasksPath(), { name }), t('tasks.created')),
        renameTask: (taskId, name) => run(() => sendJson('PATCH', `/tasks/${taskId}`, { name }), t('tasks.renamed')),
        deleteTask: (taskId) => run(() => apiFetch(`/tasks/${taskId}`, { method: 'DELETE' }), t('tasks.deleted')),
        reorder: (taskIds) =>
            run(() => sendJson('PUT', `${tasksPath()}/order`, { task_ids: taskIds }), t('tasks.orderSaved')),
    };
}
