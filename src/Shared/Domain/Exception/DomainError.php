<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use DomainException;
use Throwable;

abstract class DomainError extends DomainException
{
    /** @param array<string, string|int> $context */
    public function __construct(
        public readonly string $errorCode,
        public readonly array $context = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($errorCode, previous: $previous);
    }
}
