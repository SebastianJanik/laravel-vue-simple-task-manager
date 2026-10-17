<?php

declare(strict_types=1);

namespace Tests\Unit\Project\Domain;

use App\Project\Domain\Aggregate\Project;
use App\Project\Domain\ValueObject\ProjectId;
use App\Project\Domain\ValueObject\ProjectName;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    #[Test]
    public function a_created_project_takes_its_id_and_name_and_uses_now_for_both_timestamps(): void
    {
        $now = new DateTimeImmutable('2026-01-01 10:00:00');

        $id = ProjectId::generate();

        $project = Project::create($id, new ProjectName('Website'), $now);

        $this->assertSame($id, $project->id());
        $this->assertSame('Website', $project->name()->value);
        $this->assertEquals($now, $project->createdAt());
        $this->assertEquals($now, $project->updatedAt());
    }

    #[Test]
    public function a_reconstituted_project_keeps_all_its_stored_data(): void
    {
        $created = new DateTimeImmutable('2026-01-01 10:00:00');
        $updated = new DateTimeImmutable('2026-02-02 12:00:00');
        $id = ProjectId::generate();

        $project = Project::reconstitute($id, new ProjectName('Website'), $created, $updated);

        $this->assertSame($id, $project->id());
        $this->assertSame('Website', $project->name()->value);
        $this->assertEquals($created, $project->createdAt());
        $this->assertEquals($updated, $project->updatedAt());
    }
}
