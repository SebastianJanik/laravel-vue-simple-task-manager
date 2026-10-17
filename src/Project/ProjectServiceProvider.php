<?php

declare(strict_types=1);

namespace App\Project;

use App\Project\Application\ReadModel\ProjectReadModel;
use App\Project\Domain\ProjectRepository;
use App\Project\Infrastructure\ReadModels;
use App\Project\Infrastructure\Repositories;
use App\Shared\Presentation\ApiRoutes;
use Illuminate\Support\ServiceProvider;

final class ProjectServiceProvider extends ServiceProvider
{
    public array $singletons = [
        ProjectRepository::class => Repositories\ProjectRepository::class,
        ProjectReadModel::class => ReadModels\ProjectReadModel::class,
    ];

    public function boot(): void
    {
        ApiRoutes::load(__DIR__.'/Presentation/Routes/api.php');
    }
}
