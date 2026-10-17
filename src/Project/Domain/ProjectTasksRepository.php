<?php

declare(strict_types=1);

namespace App\Project\Domain;

use App\Project\Domain\ValueObject\ProjectId;

interface ProjectTasksRepository
{
    public function existIn(ProjectId $projectId): bool;
}
