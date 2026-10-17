<?php

declare(strict_types=1);

namespace App\Task\Presentation\Http\Requests;

use App\Task\Domain\ValueObject\TaskName;
use Illuminate\Foundation\Http\FormRequest;

final class TaskNameRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:'.TaskName::MAX_LENGTH],
        ];
    }

    public function name(): string
    {
        return $this->validated('name');
    }
}
