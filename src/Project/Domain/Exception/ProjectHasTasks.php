<?php

declare(strict_types=1);

namespace App\Project\Domain\Exception;

use App\Shared\Domain\Exception\DomainError;

final class ProjectHasTasks extends DomainError
{
    public static function create(): self
    {
        return new self('project_has_tasks');
    }
}
