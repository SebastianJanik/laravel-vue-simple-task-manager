<?php

declare(strict_types=1);

namespace Tests\Feature\Task\Application;

use App\Project\Domain\Exception\ProjectNotFound;
use App\Project\Infrastructure\Models\ProjectModel;
use App\Shared\Domain\Exception\InvalidUuid;
use App\Task\Application\Command\ReorderTasks;
use App\Task\Application\Command\ReorderTasksHandler;
use App\Task\Domain\Exception\InvalidTaskOrder;
use App\Task\Infrastructure\Models\TaskModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ReorderTasksHandlerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_the_new_order(): void
    {
        $project = ProjectModel::factory()->create();
        [$a, $b, $c] = TaskModel::factory()->inProject($project)->count(3)->create();

        $this->handle($project->id->toString(), [$c, $a, $b]);

        $this->assertDatabaseHas('tasks', ['id' => $c->id->toString(), 'priority' => 1]);
        $this->assertDatabaseHas('tasks', ['id' => $a->id->toString(), 'priority' => 2]);
        $this->assertDatabaseHas('tasks', ['id' => $b->id->toString(), 'priority' => 3]);
    }

    #[Test]
    public function it_rejects_an_order_that_misses_a_task_and_keeps_the_old_one(): void
    {
        $project = ProjectModel::factory()->create();
        [$a, $b] = TaskModel::factory()->inProject($project)->count(2)->create();

        try {
            $this->handle($project->id->toString(), [$b]);
            $this->fail('Expected InvalidTaskOrder.');
        } catch (InvalidTaskOrder) {
            $this->assertDatabaseHas('tasks', ['id' => $a->id->toString(), 'priority' => 1]);
            $this->assertDatabaseHas('tasks', ['id' => $b->id->toString(), 'priority' => 2]);
        }
    }

    #[Test]
    public function it_fails_for_an_unknown_project(): void
    {
        $this->expectException(ProjectNotFound::class);

        $this->handle((string) Str::uuid7(), []);
    }

    #[Test]
    public function it_fails_for_a_malformed_task_id(): void
    {
        $project = ProjectModel::factory()->create();

        $this->expectException(InvalidUuid::class);

        $this->app->make(ReorderTasksHandler::class)->handle(new ReorderTasks($project->id->toString(), ['nope']));
    }

    /** @param list<TaskModel> $orderedTasks */
    private function handle(string $projectId, array $orderedTasks): void
    {
        $this->app->make(ReorderTasksHandler::class)->handle(new ReorderTasks(
            $projectId,
            array_map(static fn (TaskModel $task): string => $task->id->toString(), $orderedTasks),
        ));
    }
}
