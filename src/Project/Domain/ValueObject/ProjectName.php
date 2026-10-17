<?php

declare(strict_types=1);

namespace App\Project\Domain\ValueObject;

use App\Shared\Domain\ValueObject\BoundedText;

final readonly class ProjectName extends BoundedText
{
    protected static function emptyErrorCode(): string
    {
        return 'project_name_empty';
    }

    protected static function tooLongErrorCode(): string
    {
        return 'project_name_too_long';
    }
}
