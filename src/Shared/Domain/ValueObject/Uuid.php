<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidUuid;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface;
use Stringable;

abstract readonly class Uuid implements Stringable
{
    final private function __construct(private UuidInterface $uuid) {}

    public static function generate(): static
    {
        return new static(RamseyUuid::uuid7());
    }

    public static function fromString(string $value): static
    {
        try {
            return new static(RamseyUuid::fromString($value));
        } catch (InvalidUuidStringException $e) {
            throw InvalidUuid::fromValue(static::class, $value, $e);
        }
    }

    public function equals(self $other): bool
    {
        return $other instanceof static && $this->uuid->equals($other->uuid);
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
