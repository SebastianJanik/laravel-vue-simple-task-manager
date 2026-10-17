<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidText;

abstract readonly class BoundedText
{
    public const int MAX_LENGTH = 255;

    public string $value;

    final public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidText::empty(static::emptyErrorCode());
        }

        if (mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw InvalidText::tooLong(static::tooLongErrorCode(), self::MAX_LENGTH);
        }

        $this->value = $trimmed;
    }

    public function equals(self $other): bool
    {
        return $other instanceof static && $other->value === $this->value;
    }

    abstract protected static function emptyErrorCode(): string;

    abstract protected static function tooLongErrorCode(): string;
}
