<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Project\Infrastructure\Models\ProjectModel;
use App\Task\Infrastructure\Models\TaskModel;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // run.sh seeds on every start, so skip once there's data.
        if (ProjectModel::query()->exists()) {
            return;
        }

        ProjectModel::factory()
            ->count(3)
            ->create()
            ->each(static fn (ProjectModel $project) => TaskModel::factory()->count(5)->inProject($project)->create());
    }
}
