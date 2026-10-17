<?php

declare(strict_types=1);

namespace Tests\Feature\Task\Application;

use App\Project\Infrastructure\Models\ProjectModel;
use App\Shared\Domain\Exception\InvalidText;
use App\Shared\Domain\Exception\InvalidUuid;
use App\Task\Application\Command\RenameTask;
use App\Task\Application\Command\RenameTaskHandler;
use App\Task\Domain\Exception\TaskNotFound;
use App\Task\Infrastructure\Models\TaskModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RenameTaskHandlerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renames_the_task_and_touches_its_timestamp(): void
    {
        $this->travelTo('2026-02-02 12:00:00');
        $task = TaskModel::factory()->create([
            'name' => 'Old',
            'created_at' => '2026-01-01 10:00:00',
            'updated_at' => '2026-01-01 10:00:00',
        ]);

        $this->handle($task->id->toString(), ' New ');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id->toString(),
            'name' => 'New',
            'created_at' => '2026-01-01 10:00:00',
            'updated_at' => '2026-02-02 12:00:00',
        ]);
    }

    #[Test]
    public function it_leaves_the_other_tasks_of_the_project_alone(): void
    {
        $project = ProjectModel::factory()->create();
        [$first, $second] = TaskModel::factory()->inProject($project)->count(2)->create();

        $this->handle($second->id->toString(), 'Renamed');

        $this->assertDatabaseHas('tasks', ['id' => $first->id->toString(), 'name' => $first->name, 'priority' => 1]);
        $this->assertDatabaseHas('tasks', ['id' => $second->id->toString(), 'name' => 'Renamed', 'priority' => 2]);
    }

    #[Test]
    public function it_rejects_an_invalid_name(): void
    {
        $task = TaskModel::factory()->create();

        $this->expectException(InvalidText::class);

        $this->handle($task->id->toString(), ' ');
    }

    #[Test]
    public function it_fails_for_an_unknown_task(): void
    {
        $this->expectException(TaskNotFound::class);

        $this->handle((string) Str::uuid7(), 'New');
    }

    #[Test]
    public function it_fails_for_a_malformed_task_id(): void
    {
        $this->expectException(InvalidUuid::class);

        $this->handle('nope', 'New');
    }

    private function handle(string $taskId, string $name): void
    {
        $this->app->make(RenameTaskHandler::class)->handle(new RenameTask($taskId, $name));
    }
}
