<?php

declare(strict_types=1);

namespace App\Task\Application\Query;

use App\Project\Application\ReadModel\ProjectReadModel;
use App\Project\Domain\Exception\ProjectNotFound;
use App\Project\Domain\ValueObject\ProjectId;
use App\Task\Application\ReadModel\TaskReadModel;
use App\Task\Application\ReadModel\TaskView;

final readonly class ListTasksOfProjectHandler
{
    public function __construct(
        private ProjectReadModel $projectReadModel,
        private TaskReadModel $taskReadModel,
    ) {}

    /** @return list<TaskView> */
    public function handle(ListTasksOfProject $query): array
    {
        $projectId = ProjectId::fromString($query->projectId);

        if (! $this->projectReadModel->exists($projectId)) {
            throw ProjectNotFound::withId($projectId);
        }

        return $this->taskReadModel->ofProject($projectId);
    }
}
