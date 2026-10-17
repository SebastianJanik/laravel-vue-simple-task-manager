<?php

declare(strict_types=1);

namespace App\Task\Domain\ValueObject;

use App\Shared\Domain\ValueObject\BoundedText;

final readonly class TaskName extends BoundedText
{
    protected static function emptyErrorCode(): string
    {
        return 'task_name_empty';
    }

    protected static function tooLongErrorCode(): string
    {
        return 'task_name_too_long';
    }
}
