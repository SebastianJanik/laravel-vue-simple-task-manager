<?php

declare(strict_types=1);

namespace Tests\Unit\Task\Domain;

use App\Project\Domain\ValueObject\ProjectId;
use App\Task\Domain\Aggregate\Task;
use App\Task\Domain\Aggregate\TaskList;
use App\Task\Domain\Exception\CorruptedTaskList;
use App\Task\Domain\Exception\InvalidTaskOrder;
use App\Task\Domain\Exception\TaskNotFound;
use App\Task\Domain\ValueObject\Priority;
use App\Task\Domain\ValueObject\TaskId;
use App\Task\Domain\ValueObject\TaskName;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TaskListTest extends TestCase
{
    private ProjectId $projectId;

    private DateTimeImmutable $now;

    protected function setUp(): void
    {
        $this->projectId = ProjectId::generate();
        $this->now = new DateTimeImmutable('2026-01-01 10:00:00');
    }

    #[Test]
    public function an_empty_list_is_valid(): void
    {
        $this->assertSame([], (new TaskList($this->projectId, []))->tasks());
    }

    #[Test]
    public function it_exposes_its_project_id(): void
    {
        $this->assertTrue($this->projectId->equals((new TaskList($this->projectId, []))->projectId()));
    }

    #[Test]
    public function it_sorts_tasks_by_priority_on_construction(): void
    {
        $list = new TaskList($this->projectId, [$this->task('Second', 2), $this->task('First', 1)]);

        $this->assertSame(['First', 'Second'], $this->names($list));
    }

    #[Test]
    public function it_rejects_priorities_that_do_not_start_at_one(): void
    {
        $this->expectException(CorruptedTaskList::class);

        new TaskList($this->projectId, [$this->task('A', 2), $this->task('B', 3)]);
    }

    #[Test]
    public function it_rejects_a_gap_in_the_priorities(): void
    {
        $this->expectException(CorruptedTaskList::class);

        new TaskList($this->projectId, [$this->task('A', 1), $this->task('B', 3)]);
    }

    #[Test]
    public function it_rejects_duplicate_priorities(): void
    {
        $this->expectException(CorruptedTaskList::class);

        new TaskList($this->projectId, [$this->task('A', 1), $this->task('B', 1)]);
    }

    #[Test]
    public function it_rejects_a_task_of_another_project(): void
    {
        $foreign = Task::create(TaskId::generate(), ProjectId::generate(), new TaskName('X'), new Priority(1), $this->now);

        $this->expectException(CorruptedTaskList::class);

        new TaskList($this->projectId, [$foreign]);
    }

    #[Test]
    public function appending_to_an_empty_list_gives_priority_one(): void
    {
        $list = new TaskList($this->projectId, []);

        $list->append(TaskId::generate(), new TaskName('First'), $this->now);

        $this->assertSame([1], $this->priorities($list));
    }

    #[Test]
    public function appending_puts_the_task_at_the_bottom_of_its_project(): void
    {
        $list = new TaskList($this->projectId, [$this->task('A', 1), $this->task('B', 2)]);

        $list->append(TaskId::generate(), new TaskName('C'), $this->now);

        $this->assertSame(['A', 'B', 'C'], $this->names($list));
        $this->assertSame([1, 2, 3], $this->priorities($list));
        $this->assertTrue($this->projectId->equals($list->tasks()[2]->projectId()));
    }

    #[Test]
    public function renaming_changes_only_the_name_of_that_task(): void
    {
        $target = $this->task('Old', 2);
        $list = new TaskList($this->projectId, [$this->task('A', 1), $target]);

        $list->rename($target->id(), new TaskName('New'), $this->now);

        $this->assertSame(['A', 'New'], $this->names($list));
        $this->assertSame([1, 2], $this->priorities($list));
    }

    #[Test]
    public function renaming_an_unknown_task_fails(): void
    {
        $list = new TaskList($this->projectId, [$this->task('A', 1)]);

        $this->expectException(TaskNotFound::class);

        $list->rename(TaskId::generate(), new TaskName('New'), $this->now);
    }

    #[Test]
    public function reordering_renumbers_the_tasks_in_the_given_order(): void
    {
        [$a, $b, $c] = [$this->task('A', 1), $this->task('B', 2), $this->task('C', 3)];
        $list = new TaskList($this->projectId, [$a, $b, $c]);

        $list->reorder([$c->id(), $a->id(), $b->id()], $this->now);

        $this->assertSame(['C', 'A', 'B'], $this->names($list));
        $this->assertSame([1, 2, 3], $this->priorities($list));
    }

    #[Test]
    public function reordering_leaves_unmoved_tasks_untouched(): void
    {
        [$a, $b, $c] = [$this->task('A', 1), $this->task('B', 2), $this->task('C', 3)];
        $list = new TaskList($this->projectId, [$a, $b, $c]);

        $list->reorder([$a->id(), $c->id(), $b->id()], $this->now);

        $this->assertSame($a, $list->tasks()[0]);
    }

    #[Test]
    public function reordering_with_a_missing_id_is_rejected(): void
    {
        [$a, $b] = [$this->task('A', 1), $this->task('B', 2)];
        $list = new TaskList($this->projectId, [$a, $b]);

        $this->expectException(InvalidTaskOrder::class);

        $list->reorder([$a->id()], $this->now);
    }

    #[Test]
    public function reordering_with_an_extra_id_is_rejected(): void
    {
        [$a, $b] = [$this->task('A', 1), $this->task('B', 2)];
        $list = new TaskList($this->projectId, [$a, $b]);

        $this->expectException(InvalidTaskOrder::class);

        $list->reorder([$a->id(), $b->id(), TaskId::generate()], $this->now);
    }

    #[Test]
    public function reordering_with_an_unknown_id_instead_of_a_known_one_is_rejected(): void
    {
        [$a, $b] = [$this->task('A', 1), $this->task('B', 2)];
        $list = new TaskList($this->projectId, [$a, $b]);

        $this->expectException(InvalidTaskOrder::class);

        $list->reorder([$a->id(), TaskId::generate()], $this->now);
    }

    #[Test]
    public function reordering_with_a_duplicate_id_is_rejected(): void
    {
        [$a, $b] = [$this->task('A', 1), $this->task('B', 2)];
        $list = new TaskList($this->projectId, [$a, $b]);

        $this->expectException(InvalidTaskOrder::class);

        $list->reorder([$a->id(), $a->id()], $this->now);
    }

    #[Test]
    public function a_rejected_reorder_leaves_the_list_unchanged(): void
    {
        [$a, $b] = [$this->task('A', 1), $this->task('B', 2)];
        $list = new TaskList($this->projectId, [$a, $b]);

        try {
            $list->reorder([$b->id(), $b->id()], $this->now);
        } catch (InvalidTaskOrder) {
        }

        $this->assertSame(['A', 'B'], $this->names($list));
    }

    #[Test]
    public function removing_a_task_closes_the_gap(): void
    {
        [$a, $b, $c] = [$this->task('A', 1), $this->task('B', 2), $this->task('C', 3)];
        $list = new TaskList($this->projectId, [$a, $b, $c]);

        $list->remove($a->id(), $this->now);

        $this->assertSame(['B', 'C'], $this->names($list));
        $this->assertSame([1, 2], $this->priorities($list));
    }

    #[Test]
    public function removing_the_last_task_leaves_the_others_alone(): void
    {
        [$a, $b] = [$this->task('A', 1), $this->task('B', 2)];
        $list = new TaskList($this->projectId, [$a, $b]);

        $list->remove($b->id(), $this->now);

        $this->assertSame([$a], $list->tasks());
    }

    #[Test]
    public function removing_an_unknown_task_fails(): void
    {
        $list = new TaskList($this->projectId, [$this->task('A', 1)]);

        $this->expectException(TaskNotFound::class);

        $list->remove(TaskId::generate(), $this->now);
    }

    private function task(string $name, int $priority): Task
    {
        return Task::create(TaskId::generate(), $this->projectId, new TaskName($name), new Priority($priority), $this->now);
    }

    /** @return list<string> */
    private function names(TaskList $list): array
    {
        return array_map(static fn (Task $task): string => $task->name()->value, $list->tasks());
    }

    /** @return list<int> */
    private function priorities(TaskList $list): array
    {
        return array_map(static fn (Task $task): int => $task->priority()->value, $list->tasks());
    }
}
