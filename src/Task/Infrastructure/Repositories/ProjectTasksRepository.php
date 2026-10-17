<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Repositories;

use App\Project\Domain as ProjectDomain;
use App\Project\Domain\ValueObject\ProjectId;
use App\Task\Infrastructure\Models\TaskModel;

final readonly class ProjectTasksRepository implements ProjectDomain\ProjectTasksRepository
{
    public function existIn(ProjectId $projectId): bool
    {
        return TaskModel::query()->where('project_id', $projectId->toString())->exists();
    }
}
