<?php

declare(strict_types=1);

namespace Tests\Unit\Task\Domain;

use App\Project\Domain\ValueObject\ProjectId;
use App\Task\Domain\Aggregate\Task;
use App\Task\Domain\ValueObject\Priority;
use App\Task\Domain\ValueObject\TaskId;
use App\Task\Domain\ValueObject\TaskName;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    private const string CREATED = '2026-01-01 10:00:00';

    private const string LATER = '2026-02-02 12:00:00';

    #[Test]
    public function a_created_task_has_equal_created_and_updated_timestamps(): void
    {
        $task = $this->task();

        $this->assertEquals(new DateTimeImmutable(self::CREATED), $task->createdAt());
        $this->assertEquals($task->createdAt(), $task->updatedAt());
    }

    #[Test]
    public function a_reconstituted_task_keeps_its_stored_timestamps(): void
    {
        $task = Task::reconstitute(
            TaskId::generate(),
            ProjectId::generate(),
            new TaskName('Stored'),
            new Priority(1),
            new DateTimeImmutable(self::CREATED),
            new DateTimeImmutable(self::LATER),
        );

        $this->assertEquals(new DateTimeImmutable(self::CREATED), $task->createdAt());
        $this->assertEquals(new DateTimeImmutable(self::LATER), $task->updatedAt());
    }

    #[Test]
    public function renaming_returns_a_new_task_with_the_new_name_and_updated_timestamp(): void
    {
        $task = $this->task();

        $renamed = $task->withName(new TaskName('New'), new DateTimeImmutable(self::LATER));

        $this->assertNotSame($task, $renamed);
        $this->assertSame('New', $renamed->name()->value);
        $this->assertEquals(new DateTimeImmutable(self::LATER), $renamed->updatedAt());
        $this->assertEquals($task->createdAt(), $renamed->createdAt());
        $this->assertSame('Old', $task->name()->value);
    }

    #[Test]
    public function renaming_to_the_same_name_returns_the_same_task(): void
    {
        $task = $this->task();

        $this->assertSame($task, $task->withName(new TaskName('Old'), new DateTimeImmutable(self::LATER)));
    }

    #[Test]
    public function changing_the_priority_returns_a_new_task_with_updated_timestamp(): void
    {
        $task = $this->task();

        $moved = $task->withPriority(new Priority(5), new DateTimeImmutable(self::LATER));

        $this->assertNotSame($task, $moved);
        $this->assertSame(5, $moved->priority()->value);
        $this->assertEquals(new DateTimeImmutable(self::LATER), $moved->updatedAt());
        $this->assertSame(1, $task->priority()->value);
    }

    #[Test]
    public function setting_the_same_priority_returns_the_same_task(): void
    {
        $task = $this->task();

        $this->assertSame($task, $task->withPriority(new Priority(1), new DateTimeImmutable(self::LATER)));
    }

    private function task(): Task
    {
        return Task::create(
            TaskId::generate(),
            ProjectId::generate(),
            new TaskName('Old'),
            new Priority(1),
            new DateTimeImmutable(self::CREATED),
        );
    }
}
