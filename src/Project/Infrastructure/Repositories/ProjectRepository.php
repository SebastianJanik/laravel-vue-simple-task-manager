<?php

declare(strict_types=1);

namespace App\Project\Infrastructure\Repositories;

use App\Project\Domain;
use App\Project\Domain\Aggregate\Project;
use App\Project\Domain\Exception\ProjectNotFound;
use App\Project\Domain\ValueObject\ProjectId;
use App\Project\Domain\ValueObject\ProjectName;
use App\Project\Infrastructure\Models\ProjectModel;

final readonly class ProjectRepository implements Domain\ProjectRepository
{
    public function getForUpdate(ProjectId $id): Project
    {
        $model = ProjectModel::query()->lockForUpdate()->find($id->toString()) ?? throw ProjectNotFound::withId($id);

        return $this->toDomain($model);
    }

    public function save(Project $project): void
    {
        ProjectModel::query()->upsert(
            [[
                'id' => $project->id()->toString(),
                'name' => $project->name()->value,
                'created_at' => $project->createdAt(),
                'updated_at' => $project->updatedAt(),
            ]],
            ['id'],
            ['name', 'updated_at'],
        );
    }

    public function remove(ProjectId $id): void
    {
        ProjectModel::query()->whereKey($id->toString())->delete();
    }

    private function toDomain(ProjectModel $model): Project
    {
        return Project::reconstitute(
            $model->id,
            new ProjectName($model->name),
            $model->created_at,
            $model->updated_at,
        );
    }
}
