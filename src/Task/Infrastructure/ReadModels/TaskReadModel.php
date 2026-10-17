<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\ReadModels;

use App\Project\Domain\ValueObject\ProjectId;
use App\Task\Application\ReadModel;
use App\Task\Application\ReadModel\TaskView;
use App\Task\Infrastructure\Models\TaskModel;

final readonly class TaskReadModel implements ReadModel\TaskReadModel
{
    public function ofProject(ProjectId $projectId): array
    {
        return TaskModel::query()
            ->where('project_id', $projectId->toString())
            ->orderBy('priority')
            ->get()
            ->map(static fn (TaskModel $model): TaskView => new TaskView(
                $model->id->toString(),
                $model->project_id->toString(),
                $model->name,
                $model->priority,
                $model->created_at,
                $model->updated_at,
            ))
            ->all();
    }
}
