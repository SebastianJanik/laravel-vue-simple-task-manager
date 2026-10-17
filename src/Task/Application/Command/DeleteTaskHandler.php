<?php

declare(strict_types=1);

namespace App\Task\Application\Command;

use App\Shared\Application\TransactionManager;
use App\Shared\Domain\Clock;
use App\Task\Domain\TaskListRepository;
use App\Task\Domain\ValueObject\TaskId;

final readonly class DeleteTaskHandler
{
    public function __construct(
        private TaskListRepository $taskLists,
        private TransactionManager $transactionManager,
        private Clock $clock,
    ) {}

    public function handle(DeleteTask $command): void
    {
        $taskId = TaskId::fromString($command->taskId);
        $now = $this->clock->now();

        $this->transactionManager->transactional(function () use ($taskId, $now): void {
            $taskList = $this->taskLists->lockedContaining($taskId);

            $taskList->remove($taskId, $now);

            $this->taskLists->save($taskList);
        });
    }
}
