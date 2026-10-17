<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Application\TransactionManager;
use Closure;
use Illuminate\Database\ConnectionInterface;

final readonly class DatabaseTransactionManager implements TransactionManager
{
    public function __construct(private ConnectionInterface $connection) {}

    public function transactional(Closure $operation): mixed
    {
        return $this->connection->transaction($operation);
    }
}
