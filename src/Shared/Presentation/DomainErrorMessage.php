<?php

declare(strict_types=1);

namespace App\Shared\Presentation;

use App\Shared\Domain\Exception\DomainError;

final class DomainErrorMessage
{
    public static function for(DomainError $error): string
    {
        return __('errors.'.$error->errorCode, $error->context);
    }
}
