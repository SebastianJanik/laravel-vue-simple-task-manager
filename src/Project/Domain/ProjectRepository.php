<?php

declare(strict_types=1);

namespace App\Project\Domain;

use App\Project\Domain\Aggregate\Project;
use App\Project\Domain\Exception\ProjectNotFound;
use App\Project\Domain\ValueObject\ProjectId;

interface ProjectRepository
{
    /**
     * Locks the project until the current transaction ends. Changes to the project's
     * task list take this lock first, so they never interleave.
     *
     * @throws ProjectNotFound
     */
    public function getForUpdate(ProjectId $id): Project;

    public function save(Project $project): void;

    public function remove(ProjectId $id): void;
}
