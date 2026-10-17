<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Domain\ValueObject\Uuid;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/** @implements CastsAttributes<Uuid, Uuid|string> */
final readonly class UuidCast implements CastsAttributes
{
    /** @param class-string<Uuid> $type */
    public function __construct(private string $type) {}

    public function get(Model $model, string $key, mixed $value, array $attributes): ?Uuid
    {
        return $value === null ? null : $this->type::fromString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value === null ? null : (string) $value;
    }
}
