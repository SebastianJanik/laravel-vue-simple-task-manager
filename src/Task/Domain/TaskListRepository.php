<?php

declare(strict_types=1);

namespace App\Task\Domain;

use App\Project\Domain\Exception\ProjectNotFound;
use App\Project\Domain\ValueObject\ProjectId;
use App\Task\Domain\Aggregate\TaskList;
use App\Task\Domain\Exception\TaskNotFound;
use App\Task\Domain\ValueObject\TaskId;

interface TaskListRepository
{
    /**
     * Loads the project's tasks and holds a lock on them until the current transaction ends,
     * so concurrent changes to one project's priorities never interleave.
     *
     * @throws ProjectNotFound
     */
    public function lockedForProject(ProjectId $projectId): TaskList;

    /**
     * Same as lockedForProject() for the project the task belongs to.
     *
     * @throws TaskNotFound
     */
    public function lockedContaining(TaskId $taskId): TaskList;

    /** Persists the list as given: tasks no longer in it are removed, the others inserted or updated. */
    public function save(TaskList $taskList): void;
}
