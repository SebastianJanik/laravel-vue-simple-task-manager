<?php

declare(strict_types=1);

namespace App\Task\Application\Command;

use App\Project\Domain\ValueObject\ProjectId;
use App\Shared\Application\TransactionManager;
use App\Shared\Domain\Clock;
use App\Task\Domain\TaskListRepository;
use App\Task\Domain\ValueObject\TaskId;

final readonly class ReorderTasksHandler
{
    public function __construct(
        private TaskListRepository $taskLists,
        private TransactionManager $transactionManager,
        private Clock $clock,
    ) {}

    public function handle(ReorderTasks $command): void
    {
        $projectId = ProjectId::fromString($command->projectId);
        $orderedIds = array_map(
            static fn (string $id): TaskId => TaskId::fromString($id),
            $command->orderedTaskIds,
        );
        $now = $this->clock->now();

        $this->transactionManager->transactional(function () use ($projectId, $orderedIds, $now): void {
            $taskList = $this->taskLists->lockedForProject($projectId);

            $taskList->reorder($orderedIds, $now);

            $this->taskLists->save($taskList);
        });
    }
}
