<?php

declare(strict_types=1);

namespace App\Project\Application\Command;

use App\Project\Domain\ProjectRepository;
use App\Project\Domain\ProjectTasksRepository;
use App\Project\Domain\ValueObject\ProjectId;
use App\Shared\Application\TransactionManager;

final readonly class DeleteProjectHandler
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private ProjectTasksRepository $projectTasksRepository,
        private TransactionManager $transactionManager,
    ) {}

    public function handle(DeleteProject $command): void
    {
        $projectId = ProjectId::fromString($command->projectId);

        // The project lock is what task creation takes first, so no task can appear between the check and the delete.
        $this->transactionManager->transactional(function () use ($projectId): void {
            $project = $this->projectRepository->getForUpdate($projectId);

            $project->assertDeletable($this->projectTasksRepository);

            $this->projectRepository->remove($projectId);
        });
    }
}
