<script setup>
import { computed, onMounted, ref, toRef } from 'vue';
import { useRouter } from 'vue-router';
import draggable from 'vuedraggable';
import AsyncStatus from '../components/AsyncStatus.vue';
import BaseSelect from '../components/BaseSelect.vue';
import DeleteConfirmation from '../components/DeleteConfirmation.vue';
import NameForm from '../components/NameForm.vue';
import PageLayout from '../components/PageLayout.vue';
import TaskRow from '../components/TaskRow.vue';
import { t } from '../i18n';
import { usePendingDeletion } from '../composables/usePendingDeletion';
import { useProjects } from '../composables/useProjects';
import { useTasks } from '../composables/useTasks';

const props = defineProps({
    projectId: { type: String, required: true },
});

const router = useRouter();
const { projects, fetchProjects } = useProjects();
const { tasks, loading, error, busy, createTask, renameTask, deleteTask, reorder } = useTasks(
    toRef(props, 'projectId'),
);
const { pending, ask, cancel, confirm } = usePendingDeletion((task) => deleteTask(task.id));

const form = ref(null);
const dragging = ref(false);
const projectNotFound = computed(() => error.value?.status === 404);
const dragDisabled = computed(() => loading.value || busy.value);

onMounted(fetchProjects);

function openProject(projectId) {
    router.push({ name: 'tasks', params: { projectId } });
}

async function addTask(name) {
    if (await createTask(name)) {
        form.value.reset();
    }
}

function onDragEnd(event) {
    dragging.value = false;

    if (event.oldIndex !== event.newIndex) {
        reorder(tasks.value.map((task) => task.id));
    }
}
</script>

<template>
    <PageLayout>
        <router-link :to="{ name: 'projects' }" class="text-sm text-blue-600 dark:text-blue-400">{{
            t('tasks.back')
        }}</router-link>

        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 my-4">
            <label for="project" class="font-medium">{{ t('tasks.project') }}</label>
            <BaseSelect
                id="project"
                :model-value="projectId"
                class="min-w-0 sm:flex-1"
                @update:model-value="openProject"
            >
                <option v-for="project in projects" :key="project.id" :value="project.id">
                    {{ project.name }}
                </option>
            </BaseSelect>
        </div>

        <AsyncStatus v-if="projectNotFound" :error="error.message" />

        <template v-else>
            <NameForm
                ref="form"
                :placeholder="t('tasks.newPlaceholder')"
                :button-label="t('tasks.add')"
                @submit="addTask"
            />

            <AsyncStatus
                :loading="loading"
                :error="error?.message"
                :empty="tasks.length === 0"
                :empty-message="t('tasks.empty')"
            />

            <draggable
                v-model="tasks"
                item-key="id"
                handle=".drag-handle"
                tag="ul"
                class="space-y-2"
                :class="{ dragging }"
                :animation="200"
                :disabled="dragDisabled"
                ghost-class="task-ghost"
                chosen-class="task-chosen"
                @start="dragging = true"
                @end="onDragEnd"
            >
                <template #item="{ element: task }">
                    <TaskRow
                        :task="task"
                        :drag-disabled="dragDisabled"
                        @rename="renameTask(task.id, $event)"
                        @delete="ask(task)"
                    />
                </template>
            </draggable>
        </template>

        <DeleteConfirmation :item="pending" :title="t('tasks.deleteTitle')" @confirm="confirm" @cancel="cancel" />
    </PageLayout>
</template>
