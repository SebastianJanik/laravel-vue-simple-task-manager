<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Models;

use App\Project\Domain\ValueObject\ProjectId;
use App\Shared\Infrastructure\UuidCast;
use App\Task\Domain\ValueObject\TaskId;
use Carbon\CarbonImmutable;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property TaskId $id
 * @property ProjectId $project_id
 * @property string $name
 * @property int $priority
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 */
#[Table(name: 'tasks', keyType: 'string', incrementing: false, timestamps: false)]
#[Unguarded]
#[UseFactory(TaskFactory::class)]
final class TaskModel extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => UuidCast::class.':'.TaskId::class,
            'project_id' => UuidCast::class.':'.ProjectId::class,
            'priority' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
