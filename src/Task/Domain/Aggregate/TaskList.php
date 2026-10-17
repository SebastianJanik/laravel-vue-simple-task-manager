<?php

declare(strict_types=1);

namespace App\Task\Domain\Aggregate;

use App\Project\Domain\ValueObject\ProjectId;
use App\Task\Domain\Exception\CorruptedTaskList;
use App\Task\Domain\Exception\InvalidTaskOrder;
use App\Task\Domain\Exception\TaskNotFound;
use App\Task\Domain\ValueObject\Priority;
use App\Task\Domain\ValueObject\TaskId;
use App\Task\Domain\ValueObject\TaskName;
use DateTimeImmutable;

/**
 * Aggregate root: the ordered tasks of a single project. Owns the invariant that priorities
 * are contiguous and start at 1, which no single Task can guard on its own.
 */
final class TaskList
{
    /** @var list<Task> */
    private array $tasks;

    /** @param list<Task> $tasks */
    public function __construct(private readonly ProjectId $projectId, array $tasks)
    {
        usort($tasks, static fn (Task $a, Task $b): int => $a->priority()->value <=> $b->priority()->value);

        foreach ($tasks as $index => $task) {
            if (! $task->projectId()->equals($projectId)) {
                throw CorruptedTaskList::taskOfAnotherProject($task->id(), $projectId);
            }

            if ($task->priority()->value !== $index + 1) {
                throw CorruptedTaskList::prioritiesNotContiguous($projectId);
            }
        }

        $this->tasks = $tasks;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    /** @return list<Task> ordered by priority */
    public function tasks(): array
    {
        return $this->tasks;
    }

    public function append(TaskId $id, TaskName $name, DateTimeImmutable $now): void
    {
        $this->tasks[] = Task::create($id, $this->projectId, $name, new Priority(count($this->tasks) + 1), $now);
    }

    public function rename(TaskId $id, TaskName $name, DateTimeImmutable $now): void
    {
        $index = $this->indexOf($id);

        $this->tasks[$index] = $this->tasks[$index]->withName($name, $now);
    }

    /** @param list<TaskId> $orderedIds */
    public function reorder(array $orderedIds, DateTimeImmutable $now): void
    {
        $remaining = [];
        foreach ($this->tasks as $task) {
            $remaining[$task->id()->toString()] = $task;
        }

        if (count($orderedIds) !== count($remaining)) {
            throw InvalidTaskOrder::mustContainEveryTaskOnce();
        }

        $reordered = [];
        foreach ($orderedIds as $id) {
            $reordered[] = $remaining[$id->toString()] ?? throw InvalidTaskOrder::mustContainEveryTaskOnce();
            unset($remaining[$id->toString()]);
        }

        $this->tasks = $this->renumbered($reordered, $now);
    }

    public function remove(TaskId $id, DateTimeImmutable $now): void
    {
        $remaining = $this->tasks;
        unset($remaining[$this->indexOf($id)]);

        $this->tasks = $this->renumbered(array_values($remaining), $now);
    }

    /** @throws TaskNotFound */
    private function indexOf(TaskId $id): int
    {
        foreach ($this->tasks as $index => $task) {
            if ($task->id()->equals($id)) {
                return $index;
            }
        }

        throw TaskNotFound::withId($id);
    }

    /**
     * @param  list<Task>  $tasks
     * @return list<Task>
     */
    private function renumbered(array $tasks, DateTimeImmutable $now): array
    {
        return array_map(
            static fn (Task $task, int $index): Task => $task->withPriority(new Priority($index + 1), $now),
            $tasks,
            array_keys($tasks),
        );
    }
}
