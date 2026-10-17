<?php

declare(strict_types=1);

namespace App\Task\Application\Command;

use App\Shared\Application\TransactionManager;
use App\Shared\Domain\Clock;
use App\Task\Domain\TaskListRepository;
use App\Task\Domain\ValueObject\TaskId;
use App\Task\Domain\ValueObject\TaskName;

final readonly class RenameTaskHandler
{
    public function __construct(
        private TaskListRepository $taskLists,
        private TransactionManager $transactionManager,
        private Clock $clock,
    ) {}

    public function handle(RenameTask $command): void
    {
        $taskId = TaskId::fromString($command->taskId);
        $name = new TaskName($command->name);
        $now = $this->clock->now();

        // Under the project lock like every other change, so a rename can't write back a priority a reorder just replaced.
        $this->transactionManager->transactional(function () use ($taskId, $name, $now): void {
            $taskList = $this->taskLists->lockedContaining($taskId);

            $taskList->rename($taskId, $name, $now);

            $this->taskLists->save($taskList);
        });
    }
}
