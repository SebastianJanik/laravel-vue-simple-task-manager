<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Repositories;

use App\Project\Domain as ProjectDomain;
use App\Project\Domain\ValueObject\ProjectId;
use App\Task\Domain;
use App\Task\Domain\Aggregate\Task;
use App\Task\Domain\Aggregate\TaskList;
use App\Task\Domain\Exception\TaskNotFound;
use App\Task\Domain\ValueObject\Priority;
use App\Task\Domain\ValueObject\TaskId;
use App\Task\Domain\ValueObject\TaskName;
use App\Task\Infrastructure\Models\TaskModel;

final readonly class TaskListRepository implements Domain\TaskListRepository
{
    public function __construct(private ProjectDomain\ProjectRepository $projects) {}

    public function lockedForProject(ProjectId $projectId): TaskList
    {
        $this->projects->getForUpdate($projectId);

        $tasks = TaskModel::query()
            ->where('project_id', $projectId->toString())
            ->orderBy('priority')
            ->lockForUpdate()
            ->get()
            ->map(fn (TaskModel $model): Task => $this->toDomain($model))
            ->all();

        return new TaskList($projectId, $tasks);
    }

    public function lockedContaining(TaskId $taskId): TaskList
    {
        $projectId = TaskModel::query()->whereKey($taskId->toString())->value('project_id')
            ?? throw TaskNotFound::withId($taskId);

        return $this->lockedForProject($projectId);
    }

    public function save(TaskList $taskList): void
    {
        $kept = $taskList->tasks();

        TaskModel::query()
            ->where('project_id', $taskList->projectId()->toString())
            ->whereNotIn('id', array_map(static fn (Task $task): string => $task->id()->toString(), $kept))
            ->delete();

        if ($kept === []) {
            return;
        }

        TaskModel::query()->upsert(
            array_map(static fn (Task $task): array => [
                'id' => $task->id()->toString(),
                'project_id' => $task->projectId()->toString(),
                'name' => $task->name()->value,
                'priority' => $task->priority()->value,
                'created_at' => $task->createdAt(),
                'updated_at' => $task->updatedAt(),
            ], $kept),
            ['id'],
            ['name', 'priority', 'updated_at'],
        );
    }

    private function toDomain(TaskModel $model): Task
    {
        return Task::reconstitute(
            $model->id,
            $model->project_id,
            new TaskName($model->name),
            new Priority($model->priority),
            $model->created_at,
            $model->updated_at,
        );
    }
}
