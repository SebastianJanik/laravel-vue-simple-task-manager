<?php

declare(strict_types=1);

namespace App\Task\Application\ReadModel;

use App\Project\Domain\ValueObject\ProjectId;

interface TaskReadModel
{
    /** @return list<TaskView> ordered by priority */
    public function ofProject(ProjectId $projectId): array;
}
