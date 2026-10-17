<?php

declare(strict_types=1);

namespace App\Task\Application\ReadModel;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;

final readonly class TaskView implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $name,
        public int $priority,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'name' => $this->name,
            'priority' => $this->priority,
            'created_at' => $this->createdAt->format(DateTimeInterface::ATOM),
            'updated_at' => $this->updatedAt->format(DateTimeInterface::ATOM),
        ];
    }
}
