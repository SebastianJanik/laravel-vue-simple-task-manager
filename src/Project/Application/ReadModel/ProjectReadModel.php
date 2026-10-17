<?php

declare(strict_types=1);

namespace App\Project\Application\ReadModel;

use App\Project\Domain\ValueObject\ProjectId;

interface ProjectReadModel
{
    /** @return list<ProjectView> ordered by name */
    public function all(): array;

    public function exists(ProjectId $id): bool;
}
