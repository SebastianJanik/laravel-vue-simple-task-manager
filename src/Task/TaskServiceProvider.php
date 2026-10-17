<?php

declare(strict_types=1);

namespace App\Task;

use App\Project\Domain\ProjectTasksRepository;
use App\Shared\Presentation\ApiRoutes;
use App\Task\Application\ReadModel\TaskReadModel;
use App\Task\Domain\TaskListRepository;
use App\Task\Infrastructure\ReadModels;
use App\Task\Infrastructure\Repositories;
use Illuminate\Support\ServiceProvider;

final class TaskServiceProvider extends ServiceProvider
{
    public array $singletons = [
        TaskListRepository::class => Repositories\TaskListRepository::class,
        TaskReadModel::class => ReadModels\TaskReadModel::class,
        // Project's port, answered here because only Task knows where tasks live.
        ProjectTasksRepository::class => Repositories\ProjectTasksRepository::class,
    ];

    public function boot(): void
    {
        ApiRoutes::load(__DIR__.'/Presentation/Routes/api.php');
    }
}
