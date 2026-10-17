<?php

declare(strict_types=1);

namespace App\Project\Domain\Exception;

use App\Project\Domain\ValueObject\ProjectId;
use App\Shared\Domain\Exception\NotFound;

final class ProjectNotFound extends NotFound
{
    public static function withId(ProjectId $id): self
    {
        return new self('project_not_found', ['id' => $id->toString()]);
    }
}
