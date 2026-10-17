<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain;

use App\Project\Domain\ValueObject\ProjectName;
use App\Shared\Domain\Exception\InvalidText;
use App\Shared\Domain\ValueObject\BoundedText;
use App\Task\Domain\ValueObject\TaskName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BoundedTextTest extends TestCase
{
    /** @return iterable<string, array{class-string<BoundedText>, string, string}> */
    public static function texts(): iterable
    {
        yield 'task name' => [TaskName::class, 'task_name_empty', 'task_name_too_long'];
        yield 'project name' => [ProjectName::class, 'project_name_empty', 'project_name_too_long'];
    }

    /** @param class-string<BoundedText> $class */
    #[Test]
    #[DataProvider('texts')]
    public function it_trims_the_value(string $class, string $emptyCode, string $tooLongCode): void
    {
        $this->assertSame('Write tests', (new $class("  Write tests \n"))->value);
    }

    /** @param class-string<BoundedText> $class */
    #[Test]
    #[DataProvider('texts')]
    public function it_rejects_an_empty_value(string $class, string $emptyCode, string $tooLongCode): void
    {
        $this->assertErrorCode($emptyCode, fn () => new $class(''));
    }

    /** @param class-string<BoundedText> $class */
    #[Test]
    #[DataProvider('texts')]
    public function it_rejects_a_whitespace_only_value(string $class, string $emptyCode, string $tooLongCode): void
    {
        $this->assertErrorCode($emptyCode, fn () => new $class(" \t\n "));
    }

    /** @param class-string<BoundedText> $class */
    #[Test]
    #[DataProvider('texts')]
    public function it_accepts_a_value_of_exactly_the_maximum_length(string $class, string $emptyCode, string $tooLongCode): void
    {
        $text = new $class(str_repeat('a', BoundedText::MAX_LENGTH));

        $this->assertSame(BoundedText::MAX_LENGTH, mb_strlen($text->value));
    }

    /** @param class-string<BoundedText> $class */
    #[Test]
    #[DataProvider('texts')]
    public function it_rejects_a_value_one_character_over_the_maximum(string $class, string $emptyCode, string $tooLongCode): void
    {
        $this->assertErrorCode($tooLongCode, fn () => new $class(str_repeat('a', BoundedText::MAX_LENGTH + 1)));
    }

    #[Test]
    public function it_counts_characters_not_bytes(): void
    {
        $text = new TaskName(str_repeat('ł', BoundedText::MAX_LENGTH));

        $this->assertSame(BoundedText::MAX_LENGTH, mb_strlen($text->value));
    }

    #[Test]
    public function the_too_long_error_carries_the_limit(): void
    {
        try {
            new TaskName(str_repeat('a', BoundedText::MAX_LENGTH + 1));
            $this->fail('Expected InvalidText.');
        } catch (InvalidText $e) {
            $this->assertSame(['max' => BoundedText::MAX_LENGTH], $e->context);
        }
    }

    #[Test]
    public function texts_are_equal_when_class_and_value_match(): void
    {
        $this->assertTrue((new TaskName('Same'))->equals(new TaskName(' Same ')));
    }

    #[Test]
    public function texts_are_not_equal_when_the_value_differs(): void
    {
        $this->assertFalse((new TaskName('One'))->equals(new TaskName('Two')));
    }

    #[Test]
    public function texts_of_different_classes_are_not_equal(): void
    {
        $this->assertFalse((new TaskName('Same'))->equals(new ProjectName('Same')));
    }

    private function assertErrorCode(string $expected, callable $create): void
    {
        try {
            $create();
            $this->fail('Expected InvalidText.');
        } catch (InvalidText $e) {
            $this->assertSame($expected, $e->errorCode);
        }
    }
}
