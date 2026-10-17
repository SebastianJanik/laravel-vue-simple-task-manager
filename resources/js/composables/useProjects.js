import { apiFetch, sendJson } from '../api';
import { t } from '../i18n';
import { useLoader } from './useLoader';
import { useMutation } from './useMutation';

// Module-level state: one project list shared by every page, so navigating doesn't start from an empty list.
const {
    data: projects,
    loading,
    error,
    refresh: fetchProjects,
} = useLoader(async () => (await apiFetch('/projects')).data, []);
const { run } = useMutation({ onSettled: fetchProjects });

function createProject(name) {
    return run(() => sendJson('POST', '/projects', { name }), t('projects.created'));
}

function deleteProject(projectId) {
    return run(() => apiFetch(`/projects/${projectId}`, { method: 'DELETE' }), t('projects.deleted'));
}

export function useProjects() {
    return { projects, loading, error, fetchProjects, createProject, deleteProject };
}
