<?php

declare(strict_types=1);

namespace App\Project\Application\Command;

use App\Project\Domain\Aggregate\Project;
use App\Project\Domain\ProjectRepository;
use App\Project\Domain\ValueObject\ProjectId;
use App\Project\Domain\ValueObject\ProjectName;
use App\Shared\Domain\Clock;

final readonly class CreateProjectHandler
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private Clock $clock,
    ) {}

    public function handle(CreateProject $command): ProjectId
    {
        $project = Project::create(
            ProjectId::generate(),
            new ProjectName($command->name),
            $this->clock->now(),
        );

        $this->projectRepository->save($project);

        return $project->id();
    }
}
