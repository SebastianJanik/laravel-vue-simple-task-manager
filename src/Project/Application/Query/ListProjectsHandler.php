<?php

declare(strict_types=1);

namespace App\Project\Application\Query;

use App\Project\Application\ReadModel\ProjectReadModel;
use App\Project\Application\ReadModel\ProjectView;

final readonly class ListProjectsHandler
{
    public function __construct(private ProjectReadModel $projectReadModel) {}

    /** @return list<ProjectView> */
    public function handle(): array
    {
        return $this->projectReadModel->all();
    }
}
