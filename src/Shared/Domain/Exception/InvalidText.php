<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class InvalidText extends DomainError
{
    public static function empty(string $errorCode): self
    {
        return new self($errorCode);
    }

    public static function tooLong(string $errorCode, int $max): self
    {
        return new self($errorCode, ['max' => $max]);
    }
}
