<?php

declare(strict_types=1);

namespace Tests\Feature\Project\Application;

use App\Project\Application\Command\DeleteProject;
use App\Project\Application\Command\DeleteProjectHandler;
use App\Project\Domain\Exception\ProjectHasTasks;
use App\Project\Domain\Exception\ProjectNotFound;
use App\Project\Infrastructure\Models\ProjectModel;
use App\Shared\Domain\Exception\InvalidUuid;
use App\Task\Infrastructure\Models\TaskModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DeleteProjectHandlerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_a_project_without_tasks(): void
    {
        $project = ProjectModel::factory()->create();

        $this->handle($project->id->toString());

        $this->assertDatabaseMissing('projects', ['id' => $project->id->toString()]);
    }

    #[Test]
    public function it_refuses_to_delete_a_project_with_tasks(): void
    {
        $project = ProjectModel::factory()->create();
        TaskModel::factory()->inProject($project)->create();

        try {
            $this->handle($project->id->toString());
            $this->fail('Expected ProjectHasTasks.');
        } catch (ProjectHasTasks) {
            $this->assertDatabaseHas('projects', ['id' => $project->id->toString()]);
        }
    }

    #[Test]
    public function it_fails_for_an_unknown_project(): void
    {
        $this->expectException(ProjectNotFound::class);

        $this->handle((string) Str::uuid7());
    }

    #[Test]
    public function it_fails_for_a_malformed_id(): void
    {
        $this->expectException(InvalidUuid::class);

        $this->handle('nope');
    }

    private function handle(string $projectId): void
    {
        $this->app->make(DeleteProjectHandler::class)->handle(new DeleteProject($projectId));
    }
}
