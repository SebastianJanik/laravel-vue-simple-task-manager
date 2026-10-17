<?php

declare(strict_types=1);

namespace App\Project\Application\ReadModel;

use JsonSerializable;

final readonly class ProjectView implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
