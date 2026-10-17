<?php

declare(strict_types=1);

namespace App\Task\Domain\Exception;

use App\Shared\Domain\Exception\DomainError;

final class InvalidPriority extends DomainError
{
    public static function mustBePositive(int $value): self
    {
        return new self('invalid_priority', ['given' => $value]);
    }
}
