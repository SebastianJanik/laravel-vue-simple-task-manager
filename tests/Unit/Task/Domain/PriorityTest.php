<?php

declare(strict_types=1);

namespace Tests\Unit\Task\Domain;

use App\Task\Domain\Exception\InvalidPriority;
use App\Task\Domain\ValueObject\Priority;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PriorityTest extends TestCase
{
    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_one_or_more(int $value): void
    {
        $this->assertSame($value, (new Priority($value))->value);
    }

    /** @return iterable<string, array{int}> */
    public static function validValues(): iterable
    {
        yield 'one' => [1];
        yield 'many' => [42];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_zero_and_negative_values(int $value): void
    {
        $this->expectException(InvalidPriority::class);

        new Priority($value);
    }

    /** @return iterable<string, array{int}> */
    public static function invalidValues(): iterable
    {
        yield 'zero' => [0];
        yield 'negative' => [-3];
    }

    #[Test]
    public function the_error_carries_the_given_value(): void
    {
        try {
            new Priority(-3);
            $this->fail('Expected InvalidPriority.');
        } catch (InvalidPriority $e) {
            $this->assertSame('invalid_priority', $e->errorCode);
            $this->assertSame(['given' => -3], $e->context);
        }
    }

    #[Test]
    public function priorities_with_the_same_value_are_equal(): void
    {
        $this->assertTrue((new Priority(2))->equals(new Priority(2)));
    }

    #[Test]
    public function priorities_with_different_values_are_not_equal(): void
    {
        $this->assertFalse((new Priority(2))->equals(new Priority(3)));
    }
}
