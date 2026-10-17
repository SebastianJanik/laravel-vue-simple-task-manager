<?php

declare(strict_types=1);

namespace Tests\Feature\Project\Application;

use App\Project\Application\Command\CreateProject;
use App\Project\Application\Command\CreateProjectHandler;
use App\Shared\Domain\Exception\InvalidText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateProjectHandlerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_the_project_and_returns_its_id(): void
    {
        $this->travelTo('2026-01-01 10:00:00');

        $id = $this->app->make(CreateProjectHandler::class)->handle(new CreateProject('  Website '));

        $this->assertDatabaseHas('projects', [
            'id' => $id->toString(),
            'name' => 'Website',
            'created_at' => '2026-01-01 10:00:00',
            'updated_at' => '2026-01-01 10:00:00',
        ]);
    }

    #[Test]
    public function it_rejects_an_invalid_name(): void
    {
        $this->expectException(InvalidText::class);

        $this->app->make(CreateProjectHandler::class)->handle(new CreateProject(' '));
    }
}
