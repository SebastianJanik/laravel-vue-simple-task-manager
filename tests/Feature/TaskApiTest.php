<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Project\Infrastructure\Models\ProjectModel;
use App\Task\Infrastructure\Models\TaskModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_only_the_tasks_of_the_project_ordered_by_priority(): void
    {
        $project = ProjectModel::factory()->create();
        TaskModel::factory()->inProject($project)->count(2)->sequence(['name' => 'First'], ['name' => 'Second'])->create();
        TaskModel::factory()->inProject(ProjectModel::factory()->create())->create(['name' => 'Elsewhere']);

        $this->getJson("/api/projects/{$project->id}/tasks")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'First')
            ->assertJsonPath('data.0.priority', 1)
            ->assertJsonPath('data.1.name', 'Second')
            ->assertJsonPath('data.1.priority', 2)
            ->assertJsonStructure(['data' => [['id', 'project_id', 'name', 'priority', 'created_at', 'updated_at']]]);
    }

    #[Test]
    public function listing_the_tasks_of_an_unknown_project_is_a_404(): void
    {
        $this->getJson('/api/projects/'.Str::uuid7().'/tasks')->assertNotFound();
    }

    #[Test]
    public function it_creates_a_task_at_the_bottom_of_the_project(): void
    {
        $project = ProjectModel::factory()->create();
        TaskModel::factory()->inProject($project)->count(2)->create();

        $response = $this->postJson("/api/projects/{$project->id}/tasks", ['name' => 'Last']);

        $response->assertCreated()->assertJsonStructure(['data' => ['id']]);
        $this->assertDatabaseHas('tasks', [
            'id' => $response->json('data.id'),
            'project_id' => $project->id->toString(),
            'name' => 'Last',
            'priority' => 3,
        ]);
    }

    #[Test]
    public function it_rejects_a_task_name_over_the_limit(): void
    {
        $project = ProjectModel::factory()->create();

        $this->postJson("/api/projects/{$project->id}/tasks", ['name' => str_repeat('a', 256)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
        $this->assertDatabaseCount('tasks', 0);
    }

    #[Test]
    public function creating_a_task_in_an_unknown_project_is_a_404(): void
    {
        $this->postJson('/api/projects/'.Str::uuid7().'/tasks', ['name' => 'Task'])->assertNotFound();
    }

    #[Test]
    public function it_renames_a_task(): void
    {
        $task = TaskModel::factory()->create(['name' => 'Old']);

        $this->patchJson("/api/tasks/{$task->id}", ['name' => 'New'])->assertNoContent();

        $this->assertDatabaseHas('tasks', ['id' => $task->id->toString(), 'name' => 'New']);
    }

    #[Test]
    public function renaming_an_unknown_task_is_a_404(): void
    {
        $this->patchJson('/api/tasks/'.Str::uuid7(), ['name' => 'New'])->assertNotFound();
    }

    #[Test]
    public function deleting_a_task_keeps_the_remaining_priorities_contiguous(): void
    {
        $project = ProjectModel::factory()->create();
        [$first, $second, $third] = TaskModel::factory()->inProject($project)->count(3)->create();

        $this->deleteJson("/api/tasks/{$first->id}")->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $first->id->toString()]);
        $this->assertDatabaseHas('tasks', ['id' => $second->id->toString(), 'priority' => 1]);
        $this->assertDatabaseHas('tasks', ['id' => $third->id->toString(), 'priority' => 2]);
    }

    #[Test]
    public function deleting_an_unknown_task_is_a_404(): void
    {
        $this->deleteJson('/api/tasks/'.Str::uuid7())->assertNotFound();
    }

    #[Test]
    public function it_persists_a_new_task_order(): void
    {
        $project = ProjectModel::factory()->create();
        [$first, $second, $third] = TaskModel::factory()->inProject($project)->count(3)->create();

        $this->putJson("/api/projects/{$project->id}/tasks/order", [
            'task_ids' => [$third->id->toString(), $first->id->toString(), $second->id->toString()],
        ])->assertNoContent();

        $this->assertDatabaseHas('tasks', ['id' => $third->id->toString(), 'priority' => 1]);
        $this->assertDatabaseHas('tasks', ['id' => $first->id->toString(), 'priority' => 2]);
        $this->assertDatabaseHas('tasks', ['id' => $second->id->toString(), 'priority' => 3]);
    }

    #[Test]
    public function it_rejects_an_order_that_misses_a_task(): void
    {
        $project = ProjectModel::factory()->create();
        [$first] = TaskModel::factory()->inProject($project)->count(2)->create();

        $this->putJson("/api/projects/{$project->id}/tasks/order", ['task_ids' => [$first->id->toString()]])
            ->assertUnprocessable()
            ->assertExactJson(['message' => __('errors.invalid_task_order')]);
    }

    #[Test]
    public function it_rejects_an_order_with_duplicate_ids(): void
    {
        $project = ProjectModel::factory()->create();
        [$first] = TaskModel::factory()->inProject($project)->count(2)->create();
        $id = $first->id->toString();

        $this->putJson("/api/projects/{$project->id}/tasks/order", ['task_ids' => [$id, $id]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('task_ids.0');
    }

    #[Test]
    public function it_rejects_an_order_payload_without_task_ids(): void
    {
        $project = ProjectModel::factory()->create();

        $this->putJson("/api/projects/{$project->id}/tasks/order", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('task_ids');
    }

    #[Test]
    public function reordering_an_unknown_project_is_a_404(): void
    {
        $this->putJson('/api/projects/'.Str::uuid7().'/tasks/order', ['task_ids' => []])->assertNotFound();
    }
}
