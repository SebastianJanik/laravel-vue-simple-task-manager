<?php

declare(strict_types=1);

namespace App\Task\Application\Command;

use App\Project\Domain\ValueObject\ProjectId;
use App\Shared\Application\TransactionManager;
use App\Shared\Domain\Clock;
use App\Task\Domain\TaskListRepository;
use App\Task\Domain\ValueObject\TaskId;
use App\Task\Domain\ValueObject\TaskName;

final readonly class CreateTaskHandler
{
    public function __construct(
        private TaskListRepository $taskLists,
        private TransactionManager $transactionManager,
        private Clock $clock,
    ) {}

    public function handle(CreateTask $command): TaskId
    {
        $projectId = ProjectId::fromString($command->projectId);
        $taskId = TaskId::generate();
        $name = new TaskName($command->name);
        $now = $this->clock->now();

        $this->transactionManager->transactional(function () use ($projectId, $taskId, $name, $now): void {
            $taskList = $this->taskLists->lockedForProject($projectId);

            $taskList->append($taskId, $name, $now);

            $this->taskLists->save($taskList);
        });

        return $taskId;
    }
}
