<?php

declare(strict_types=1);

namespace App\Project\Infrastructure\Models;

use App\Project\Domain\ValueObject\ProjectId;
use App\Shared\Infrastructure\UuidCast;
use Carbon\CarbonImmutable;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property ProjectId $id
 * @property string $name
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 */
#[Table(name: 'projects', keyType: 'string', incrementing: false, timestamps: false)]
#[Unguarded]
#[UseFactory(ProjectFactory::class)]
final class ProjectModel extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => UuidCast::class.':'.ProjectId::class,
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
