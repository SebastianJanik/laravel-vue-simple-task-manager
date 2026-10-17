<?php

declare(strict_types=1);

namespace App\Project\Domain\Aggregate;

use App\Project\Domain\Exception\ProjectHasTasks;
use App\Project\Domain\ProjectTasksRepository;
use App\Project\Domain\ValueObject\ProjectId;
use App\Project\Domain\ValueObject\ProjectName;
use DateTimeImmutable;

/** A named container for tasks. No use case changes a project after creation, so it stays an immutable record. */
final readonly class Project
{
    private function __construct(
        private ProjectId $id,
        private ProjectName $name,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(ProjectId $id, ProjectName $name, DateTimeImmutable $now): self
    {
        return new self($id, $name, $now, $now);
    }

    public static function reconstitute(
        ProjectId $id,
        ProjectName $name,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $name, $createdAt, $updatedAt);
    }

    public function assertDeletable(ProjectTasksRepository $projectTasksRepository): void
    {
        if ($projectTasksRepository->existIn($this->id)) {
            throw ProjectHasTasks::create();
        }
    }

    public function id(): ProjectId
    {
        return $this->id;
    }

    public function name(): ProjectName
    {
        return $this->name;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
