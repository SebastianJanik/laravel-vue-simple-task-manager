<?php

declare(strict_types=1);

namespace App\Task\Domain\ValueObject;

use App\Task\Domain\Exception\InvalidPriority;

final readonly class Priority
{
    public function __construct(public int $value)
    {
        if ($value < 1) {
            throw InvalidPriority::mustBePositive($value);
        }
    }

    public function equals(self $other): bool
    {
        return $other->value === $this->value;
    }
}
