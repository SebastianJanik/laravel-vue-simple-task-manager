<?php

declare(strict_types=1);

namespace Tests\Feature\Project\Application;

use App\Project\Application\Query\ListProjectsHandler;
use App\Project\Infrastructure\Models\ProjectModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ListProjectsHandlerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_all_projects_ordered_by_name(): void
    {
        $beta = ProjectModel::factory()->create(['name' => 'Beta']);
        $alpha = ProjectModel::factory()->create(['name' => 'Alpha']);

        $views = $this->app->make(ListProjectsHandler::class)->handle();

        $this->assertSame(
            [[$alpha->id->toString(), 'Alpha'], [$beta->id->toString(), 'Beta']],
            array_map(static fn ($view): array => [$view->id, $view->name], $views),
        );
    }

    #[Test]
    public function it_returns_nothing_when_there_are_no_projects(): void
    {
        $this->assertSame([], $this->app->make(ListProjectsHandler::class)->handle());
    }
}
