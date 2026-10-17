<?php

declare(strict_types=1);

use App\Project\ProjectServiceProvider;
use App\Shared\SharedServiceProvider;
use App\Task\TaskServiceProvider;

return [
    SharedServiceProvider::class,
    ProjectServiceProvider::class,
    TaskServiceProvider::class,
];
