<?php

declare(strict_types=1);

namespace App\Task\Application\Command;

final readonly class RenameTask
{
    public function __construct(
        public string $taskId,
        public string $name,
    ) {}
}
