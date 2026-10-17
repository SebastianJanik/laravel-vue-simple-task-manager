<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Project\Infrastructure\Models\ProjectModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ProjectModel> */
final class ProjectFactory extends Factory
{
    protected $model = ProjectModel::class;

    public function definition(): array
    {
        $now = CarbonImmutable::now();

        return [
            'id' => Str::uuid7()->toString(),
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
