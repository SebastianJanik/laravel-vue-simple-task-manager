<?php

declare(strict_types=1);

namespace App\Project\Presentation\Http\Controllers;

use App\Project\Application\Command\CreateProject;
use App\Project\Application\Command\CreateProjectHandler;
use App\Project\Application\Command\DeleteProject;
use App\Project\Application\Command\DeleteProjectHandler;
use App\Project\Application\Query\ListProjectsHandler;
use App\Project\Presentation\Http\Requests\StoreProjectRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final readonly class ProjectController
{
    public function index(ListProjectsHandler $handler): JsonResponse
    {
        return response()->json(['data' => $handler->handle()]);
    }

    public function store(StoreProjectRequest $request, CreateProjectHandler $handler): JsonResponse
    {
        $projectId = $handler->handle(new CreateProject($request->name()));

        return response()->json(['data' => ['id' => $projectId->toString()]], Response::HTTP_CREATED);
    }

    public function destroy(string $project, DeleteProjectHandler $handler): Response
    {
        $handler->handle(new DeleteProject($project));

        return response()->noContent();
    }
}
