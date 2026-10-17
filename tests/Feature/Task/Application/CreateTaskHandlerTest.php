<?php

declare(strict_types=1);

namespace Tests\Feature\Task\Application;

use App\Project\Domain\Exception\ProjectNotFound;
use App\Project\Infrastructure\Models\ProjectModel;
use App\Shared\Domain\Exception\InvalidText;
use App\Shared\Domain\Exception\InvalidUuid;
use App\Task\Application\Command\CreateTask;
use App\Task\Application\Command\CreateTaskHandler;
use App\Task\Infrastructure\Models\TaskModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateTaskHandlerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_appends_the_task_at_the_bottom_of_the_project(): void
    {
        $this->travelTo('2026-01-01 10:00:00');
        $project = ProjectModel::factory()->create();
        TaskModel::factory()->inProject($project)->count(2)->create();

        $id = $this->handle($project->id->toString(), ' Last ');

        $this->assertDatabaseHas('tasks', [
            'id' => $id,
            'project_id' => $project->id->toString(),
            'name' => 'Last',
            'priority' => 3,
            'created_at' => '2026-01-01 10:00:00',
        ]);
    }

    #[Test]
    public function it_starts_an_empty_project_at_priority_one(): void
    {
        $project = ProjectModel::factory()->create();

        $id = $this->handle($project->id->toString(), 'First');

        $this->assertDatabaseHas('tasks', ['id' => $id, 'priority' => 1]);
    }

    #[Test]
    public function it_rejects_an_invalid_name(): void
    {
        $project = ProjectModel::factory()->create();

        $this->expectException(InvalidText::class);

        $this->handle($project->id->toString(), '');
    }

    #[Test]
    public function it_fails_for_an_unknown_project(): void
    {
        $this->expectException(ProjectNotFound::class);

        $this->handle((string) Str::uuid7(), 'Task');
    }

    #[Test]
    public function it_fails_for_a_malformed_project_id(): void
    {
        $this->expectException(InvalidUuid::class);

        $this->handle('nope', 'Task');
    }

    private function handle(string $projectId, string $name): string
    {
        return $this->app->make(CreateTaskHandler::class)->handle(new CreateTask($projectId, $name))->toString();
    }
}
