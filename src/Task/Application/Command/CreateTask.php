<?php

declare(strict_types=1);

namespace App\Task\Application\Command;

final readonly class CreateTask
{
    public function __construct(
        public string $projectId,
        public string $name,
    ) {}
}
