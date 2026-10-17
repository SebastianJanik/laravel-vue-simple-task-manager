<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Throwable;

final class InvalidUuid extends DomainError
{
    public static function fromValue(string $type, string $value, ?Throwable $previous = null): self
    {
        return new self('invalid_uuid', ['type' => $type, 'value' => $value], $previous);
    }
}
