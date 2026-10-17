<?php

declare(strict_types=1);

namespace App\Task\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTaskOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'task_ids' => ['present', 'list'],
            'task_ids.*' => ['required', 'uuid', 'distinct'],
        ];
    }

    /** @return list<string> */
    public function taskIds(): array
    {
        return $this->validated('task_ids');
    }
}
