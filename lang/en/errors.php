<?php

declare(strict_types=1);

// Keys are DomainError::$errorCode values.
return [
    'task_name_empty' => 'Task name must not be empty.',
    'task_name_too_long' => 'Task name must not be longer than :max characters.',
    'project_name_empty' => 'Project name must not be empty.',
    'project_name_too_long' => 'Project name must not be longer than :max characters.',
    'invalid_uuid' => '":value" is not a valid UUID.',
    'invalid_priority' => 'Priority must be 1 or greater, :given given.',
    'invalid_task_order' => 'The new order must contain every task of the project exactly once.',
    'project_has_tasks' => 'This project still has tasks, so it cannot be deleted. Delete its tasks first.',
    'project_not_found' => 'Project ":id" does not exist.',
    'task_not_found' => 'Task ":id" does not exist.',
];
