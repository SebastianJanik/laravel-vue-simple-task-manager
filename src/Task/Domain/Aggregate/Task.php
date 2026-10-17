<?php

declare(strict_types=1);

namespace App\Task\Domain\Aggregate;

use App\Project\Domain\ValueObject\ProjectId;
use App\Task\Domain\ValueObject\Priority;
use App\Task\Domain\ValueObject\TaskId;
use App\Task\Domain\ValueObject\TaskName;
use DateTimeImmutable;

/** Immutable: only a TaskList can put a changed copy back, so it alone decides which priorities are valid. */
final readonly class Task
{
    private function __construct(
        private TaskId $id,
        private ProjectId $projectId,
        private TaskName $name,
        private Priority $priority,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        TaskId $id,
        ProjectId $projectId,
        TaskName $name,
        Priority $priority,
        DateTimeImmutable $now,
    ): self {
        return new self($id, $projectId, $name, $priority, $now, $now);
    }

    public static function reconstitute(
        TaskId $id,
        ProjectId $projectId,
        TaskName $name,
        Priority $priority,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $projectId, $name, $priority, $createdAt, $updatedAt);
    }

    public function id(): TaskId
    {
        return $this->id;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function name(): TaskName
    {
        return $this->name;
    }

    public function priority(): Priority
    {
        return $this->priority;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function withName(TaskName $name, DateTimeImmutable $now): self
    {
        if ($name->equals($this->name)) {
            return $this;
        }

        return new self($this->id, $this->projectId, $name, $this->priority, $this->createdAt, $now);
    }

    public function withPriority(Priority $priority, DateTimeImmutable $now): self
    {
        if ($priority->equals($this->priority)) {
            return $this;
        }

        return new self($this->id, $this->projectId, $this->name, $priority, $this->createdAt, $now);
    }
}
