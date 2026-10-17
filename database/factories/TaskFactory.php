<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Project\Infrastructure\Models\ProjectModel;
use App\Task\Infrastructure\Models\TaskModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Str;

/** @extends Factory<TaskModel> */
final class TaskFactory extends Factory
{
    protected $model = TaskModel::class;

    public function definition(): array
    {
        $now = CarbonImmutable::now();

        return [
            'id' => Str::uuid7()->toString(),
            'project_id' => ProjectModel::factory(),
            'name' => rtrim(fake()->sentence(4), '.'),
            'priority' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /** Priorities are contiguous from 1 within one create() call, as the domain requires. */
    public function inProject(ProjectModel $project): static
    {
        return $this
            ->state(['project_id' => $project->id])
            ->sequence(static fn (Sequence $sequence): array => ['priority' => $sequence->index + 1]);
    }
}
