<script setup>
import { onMounted, ref } from 'vue';
import AsyncStatus from '../components/AsyncStatus.vue';
import DeleteConfirmation from '../components/DeleteConfirmation.vue';
import ItemRow from '../components/ItemRow.vue';
import NameForm from '../components/NameForm.vue';
import PageLayout from '../components/PageLayout.vue';
import { t } from '../i18n';
import { useProjects } from '../composables/useProjects';
import { usePendingDeletion } from '../composables/usePendingDeletion';

const { projects, loading, error, fetchProjects, createProject, deleteProject } = useProjects();
const { pending, ask, cancel, confirm } = usePendingDeletion((project) => deleteProject(project.id));
const form = ref(null);

onMounted(fetchProjects);

async function addProject(name) {
    if (await createProject(name)) {
        form.value.reset();
    }
}
</script>

<template>
    <PageLayout>
        <h1 class="text-2xl font-semibold mb-4">{{ t('projects.title') }}</h1>

        <NameForm
            ref="form"
            :placeholder="t('projects.newPlaceholder')"
            :button-label="t('projects.add')"
            @submit="addProject"
        />

        <AsyncStatus
            :loading="loading"
            :error="error?.message"
            :empty="projects.length === 0"
            :empty-message="t('projects.empty')"
        />

        <ul class="space-y-2">
            <ItemRow
                v-for="project in projects"
                :key="project.id"
                :name="project.name"
                clickable
                @delete="ask(project)"
            >
                <router-link
                    :to="{ name: 'tasks', params: { projectId: project.id } }"
                    class="flex-1 min-w-0 px-1 py-1 break-words after:absolute after:inset-0"
                >
                    {{ project.name }}
                </router-link>
            </ItemRow>
        </ul>

        <DeleteConfirmation :item="pending" :title="t('projects.deleteTitle')" @confirm="confirm" @cancel="cancel" />
    </PageLayout>
</template>
