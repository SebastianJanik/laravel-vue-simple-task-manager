import { createRouter, createWebHistory } from 'vue-router';

export const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', name: 'projects', component: () => import('./pages/ProjectsPage.vue') },
        {
            path: '/projects/:projectId',
            name: 'tasks',
            component: () => import('./pages/TasksPage.vue'),
            props: true,
        },
    ],
});
