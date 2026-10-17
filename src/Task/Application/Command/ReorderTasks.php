<?php

declare(strict_types=1);

namespace App\Task\Application\Command;

final readonly class ReorderTasks
{
    /** @param list<string> $orderedTaskIds */
    public function __construct(
        public string $projectId,
        public array $orderedTaskIds,
    ) {}
}
