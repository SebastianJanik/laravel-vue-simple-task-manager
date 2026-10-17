<?php

declare(strict_types=1);

namespace Tests\Feature\Task\Application;

use App\Project\Infrastructure\Models\ProjectModel;
use App\Shared\Domain\Exception\InvalidUuid;
use App\Task\Application\Command\DeleteTask;
use App\Task\Application\Command\DeleteTaskHandler;
use App\Task\Domain\Exception\TaskNotFound;
use App\Task\Infrastructure\Models\TaskModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DeleteTaskHandlerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_the_task_and_closes_the_gap(): void
    {
        $project = ProjectModel::factory()->create();
        [$first, $second, $third] = TaskModel::factory()->inProject($project)->count(3)->create();

        $this->handle($first->id->toString());

        $this->assertDatabaseMissing('tasks', ['id' => $first->id->toString()]);
        $this->assertDatabaseHas('tasks', ['id' => $second->id->toString(), 'priority' => 1]);
        $this->assertDatabaseHas('tasks', ['id' => $third->id->toString(), 'priority' => 2]);
    }

    #[Test]
    public function it_leaves_the_tasks_of_other_projects_alone(): void
    {
        $task = TaskModel::factory()->inProject(ProjectModel::factory()->create())->create();
        $other = TaskModel::factory()->inProject(ProjectModel::factory()->create())->create();

        $this->handle($task->id->toString());

        $this->assertDatabaseHas('tasks', ['id' => $other->id->toString(), 'priority' => 1]);
    }

    #[Test]
    public function it_fails_for_an_unknown_task(): void
    {
        $this->expectException(TaskNotFound::class);

        $this->handle((string) Str::uuid7());
    }

    #[Test]
    public function it_fails_for_a_malformed_task_id(): void
    {
        $this->expectException(InvalidUuid::class);

        $this->handle('nope');
    }

    private function handle(string $taskId): void
    {
        $this->app->make(DeleteTaskHandler::class)->handle(new DeleteTask($taskId));
    }
}
