<?php

declare(strict_types=1);

namespace App\Task\Domain\Exception;

use App\Shared\Domain\Exception\NotFound;
use App\Task\Domain\ValueObject\TaskId;

final class TaskNotFound extends NotFound
{
    public static function withId(TaskId $id): self
    {
        return new self('task_not_found', ['id' => $id->toString()]);
    }
}
