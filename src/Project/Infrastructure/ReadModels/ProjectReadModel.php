<?php

declare(strict_types=1);

namespace App\Project\Infrastructure\ReadModels;

use App\Project\Application\ReadModel;
use App\Project\Application\ReadModel\ProjectView;
use App\Project\Domain\ValueObject\ProjectId;
use App\Project\Infrastructure\Models\ProjectModel;

final readonly class ProjectReadModel implements ReadModel\ProjectReadModel
{
    public function all(): array
    {
        return ProjectModel::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(static fn (ProjectModel $model): ProjectView => new ProjectView($model->id->toString(), $model->name))
            ->all();
    }

    public function exists(ProjectId $id): bool
    {
        return ProjectModel::query()->whereKey($id->toString())->exists();
    }
}
