<?php

declare(strict_types=1);

namespace Tests\Feature\Task\Application;

use App\Project\Domain\Exception\ProjectNotFound;
use App\Project\Infrastructure\Models\ProjectModel;
use App\Task\Application\Query\ListTasksOfProject;
use App\Task\Application\Query\ListTasksOfProjectHandler;
use App\Task\Infrastructure\Models\TaskModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ListTasksOfProjectHandlerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_only_the_tasks_of_the_project_ordered_by_priority(): void
    {
        $project = ProjectModel::factory()->create();
        TaskModel::factory()->inProject($project)->count(2)->sequence(['name' => 'First'], ['name' => 'Second'])->create();
        TaskModel::factory()->inProject(ProjectModel::factory()->create())->create();

        $views = $this->app->make(ListTasksOfProjectHandler::class)->handle(new ListTasksOfProject($project->id->toString()));

        $this->assertSame(
            [['First', 1], ['Second', 2]],
            array_map(static fn ($view): array => [$view->name, $view->priority], $views),
        );
    }

    #[Test]
    public function it_returns_nothing_for_a_project_without_tasks(): void
    {
        $project = ProjectModel::factory()->create();

        $this->assertSame([], $this->app->make(ListTasksOfProjectHandler::class)->handle(new ListTasksOfProject($project->id->toString())));
    }

    #[Test]
    public function it_fails_for_an_unknown_project(): void
    {
        $this->expectException(ProjectNotFound::class);

        $this->app->make(ListTasksOfProjectHandler::class)->handle(new ListTasksOfProject((string) Str::uuid7()));
    }
}
