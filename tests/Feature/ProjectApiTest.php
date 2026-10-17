<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Project\Infrastructure\Models\ProjectModel;
use App\Task\Infrastructure\Models\TaskModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_projects(): void
    {
        $project = ProjectModel::factory()->create(['name' => 'Website']);

        $this->getJson('/api/projects')
            ->assertOk()
            ->assertExactJson(['data' => [['id' => $project->id->toString(), 'name' => 'Website']]]);
    }

    #[Test]
    public function it_creates_a_project(): void
    {
        $response = $this->postJson('/api/projects', ['name' => '  Website ']);

        $response->assertCreated()->assertJsonStructure(['data' => ['id']]);
        $this->assertDatabaseHas('projects', ['id' => $response->json('data.id'), 'name' => 'Website']);
    }

    #[Test]
    public function it_rejects_a_project_without_a_name(): void
    {
        $this->postJson('/api/projects', ['name' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
        $this->assertDatabaseCount('projects', 0);
    }

    #[Test]
    public function it_deletes_an_empty_project(): void
    {
        $project = ProjectModel::factory()->create();

        $this->deleteJson("/api/projects/{$project->id}")->assertNoContent();

        $this->assertDatabaseMissing('projects', ['id' => $project->id->toString()]);
    }

    #[Test]
    public function it_refuses_to_delete_a_project_with_tasks(): void
    {
        $project = ProjectModel::factory()->create();
        TaskModel::factory()->inProject($project)->create();

        $this->deleteJson("/api/projects/{$project->id}")
            ->assertUnprocessable()
            ->assertExactJson(['message' => __('errors.project_has_tasks')]);

        $this->assertDatabaseHas('projects', ['id' => $project->id->toString()]);
    }

    #[Test]
    public function deleting_an_unknown_project_is_a_404(): void
    {
        $this->deleteJson('/api/projects/'.Str::uuid7())
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }

    #[Test]
    public function a_malformed_project_id_is_a_404(): void
    {
        $this->getJson('/api/projects/not-a-uuid/tasks')->assertNotFound();
        $this->deleteJson('/api/projects/not-a-uuid')->assertNotFound();
        $this->patchJson('/api/tasks/not-a-uuid', ['name' => 'New'])->assertNotFound();
    }

    #[Test]
    public function an_unknown_api_url_returns_a_json_404(): void
    {
        $this->get('/api/nothing-here')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/json');
    }
}
