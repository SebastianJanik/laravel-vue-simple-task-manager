<?php

declare(strict_types=1);

namespace App\Project\Presentation\Http\Requests;

use App\Project\Domain\ValueObject\ProjectName;
use Illuminate\Foundation\Http\FormRequest;

final class StoreProjectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:'.ProjectName::MAX_LENGTH],
        ];
    }

    public function name(): string
    {
        return $this->validated('name');
    }
}
