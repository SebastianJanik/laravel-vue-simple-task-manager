<?php

declare(strict_types=1);

namespace App\Project\Application\Command;

final readonly class DeleteProject
{
    public function __construct(public string $projectId) {}
}
