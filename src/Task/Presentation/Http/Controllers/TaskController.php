<?php

declare(strict_types=1);

namespace App\Task\Presentation\Http\Controllers;

use App\Task\Application\Command\CreateTask;
use App\Task\Application\Command\CreateTaskHandler;
use App\Task\Application\Command\DeleteTask;
use App\Task\Application\Command\DeleteTaskHandler;
use App\Task\Application\Command\RenameTask;
use App\Task\Application\Command\RenameTaskHandler;
use App\Task\Application\Command\ReorderTasks;
use App\Task\Application\Command\ReorderTasksHandler;
use App\Task\Application\Query\ListTasksOfProject;
use App\Task\Application\Query\ListTasksOfProjectHandler;
use App\Task\Presentation\Http\Requests\TaskNameRequest;
use App\Task\Presentation\Http\Requests\UpdateTaskOrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final readonly class TaskController
{
    public function index(string $project, ListTasksOfProjectHandler $handler): JsonResponse
    {
        return response()->json(['data' => $handler->handle(new ListTasksOfProject($project))]);
    }

    public function store(string $project, TaskNameRequest $request, CreateTaskHandler $handler): JsonResponse
    {
        $taskId = $handler->handle(new CreateTask($project, $request->name()));

        return response()->json(['data' => ['id' => $taskId->toString()]], Response::HTTP_CREATED);
    }

    public function update(string $task, TaskNameRequest $request, RenameTaskHandler $handler): Response
    {
        $handler->handle(new RenameTask($task, $request->name()));

        return response()->noContent();
    }

    public function destroy(string $task, DeleteTaskHandler $handler): Response
    {
        $handler->handle(new DeleteTask($task));

        return response()->noContent();
    }

    public function reorder(string $project, UpdateTaskOrderRequest $request, ReorderTasksHandler $handler): Response
    {
        $handler->handle(new ReorderTasks($project, $request->taskIds()));

        return response()->noContent();
    }
}
