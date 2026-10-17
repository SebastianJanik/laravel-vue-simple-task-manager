<?php

declare(strict_types=1);

namespace App\Task\Domain\Exception;

use App\Project\Domain\ValueObject\ProjectId;
use App\Task\Domain\ValueObject\TaskId;
use LogicException;

/** Stored data breaks an invariant the domain guarantees. A server fault, never caused by user input. */
final class CorruptedTaskList extends LogicException
{
    public static function taskOfAnotherProject(TaskId $taskId, ProjectId $expectedProjectId): self
    {
        return new self(sprintf('Task "%s" does not belong to project "%s".', $taskId->toString(), $expectedProjectId->toString()));
    }

    public static function prioritiesNotContiguous(ProjectId $projectId): self
    {
        return new self(sprintf('Priorities of project "%s" are not contiguous from 1.', $projectId->toString()));
    }
}
