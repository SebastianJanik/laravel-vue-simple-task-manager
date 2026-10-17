<?php

declare(strict_types=1);

namespace App\Task\Application\Query;

final readonly class ListTasksOfProject
{
    public function __construct(public string $projectId) {}
}
