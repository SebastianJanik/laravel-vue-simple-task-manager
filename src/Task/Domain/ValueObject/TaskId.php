<?php

declare(strict_types=1);

namespace App\Task\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Uuid;

final readonly class TaskId extends Uuid {}
