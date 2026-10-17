<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Domain\Clock;
use Carbon\CarbonImmutable;
use DateTimeImmutable;

final readonly class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return CarbonImmutable::now();
    }
}
