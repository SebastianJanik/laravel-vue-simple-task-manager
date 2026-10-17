<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain;

use App\Project\Domain\ValueObject\ProjectId;
use App\Shared\Domain\Exception\InvalidUuid;
use App\Task\Domain\ValueObject\TaskId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    #[Test]
    public function it_generates_a_valid_version_7_uuid(): void
    {
        $id = TaskId::generate();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $id->toString(),
        );
    }

    #[Test]
    public function it_parses_a_valid_string_and_prints_it_back(): void
    {
        $id = TaskId::fromString('0199c1a2-7b3c-7d4e-8f50-123456789abc');

        $this->assertSame('0199c1a2-7b3c-7d4e-8f50-123456789abc', (string) $id);
    }

    #[Test]
    public function it_normalises_the_case(): void
    {
        $id = TaskId::fromString('0199C1A2-7B3C-7D4E-8F50-123456789ABC');

        $this->assertSame('0199c1a2-7b3c-7d4e-8f50-123456789abc', $id->toString());
    }

    #[Test]
    public function it_rejects_a_malformed_string(): void
    {
        $this->expectException(InvalidUuid::class);

        TaskId::fromString('not-a-uuid');
    }

    #[Test]
    public function the_invalid_uuid_error_carries_the_type_and_value(): void
    {
        try {
            TaskId::fromString('nope');
            $this->fail('Expected InvalidUuid.');
        } catch (InvalidUuid $e) {
            $this->assertSame('invalid_uuid', $e->errorCode);
            $this->assertSame(['type' => TaskId::class, 'value' => 'nope'], $e->context);
        }
    }

    #[Test]
    public function ids_with_the_same_value_are_equal(): void
    {
        $value = TaskId::generate()->toString();

        $this->assertTrue(TaskId::fromString($value)->equals(TaskId::fromString($value)));
    }

    #[Test]
    public function ids_with_different_values_are_not_equal(): void
    {
        $this->assertFalse(TaskId::generate()->equals(TaskId::generate()));
    }

    #[Test]
    public function ids_of_different_classes_are_not_equal_even_with_the_same_value(): void
    {
        $value = TaskId::generate()->toString();

        $this->assertFalse(TaskId::fromString($value)->equals(ProjectId::fromString($value)));
    }
}
