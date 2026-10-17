<?php

declare(strict_types=1);

namespace App\Task\Domain\Exception;

use App\Shared\Domain\Exception\DomainError;

final class InvalidTaskOrder extends DomainError
{
    public static function mustContainEveryTaskOnce(): self
    {
        return new self('invalid_task_order');
    }
}
