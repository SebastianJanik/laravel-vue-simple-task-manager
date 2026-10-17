<?php

declare(strict_types=1);

namespace App\Shared;

use App\Shared\Application\TransactionManager;
use App\Shared\Domain\Clock;
use App\Shared\Domain\Exception\DomainError;
use App\Shared\Domain\Exception\NotFound;
use App\Shared\Infrastructure\DatabaseTransactionManager;
use App\Shared\Infrastructure\SystemClock;
use App\Shared\Presentation\DomainErrorMessage;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response;

final class SharedServiceProvider extends ServiceProvider
{
    public array $singletons = [
        TransactionManager::class => DatabaseTransactionManager::class,
        Clock::class => SystemClock::class,
    ];

    public function boot(): void
    {
        Route::middleware('web')->group(__DIR__.'/Presentation/Routes/web.php');

        $this->registerExceptionRendering();
    }

    private function registerExceptionRendering(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);

        // Collision (dev, console only) wraps the handler; HTTP rendering never goes through it.
        if (! $handler instanceof Handler) {
            return;
        }

        $handler->shouldRenderJsonWhen(
            static fn (Request $request): bool => $request->is('api/*') || $request->expectsJson(),
        );

        // NotFound extends DomainError, so it must be registered first.
        $handler->renderable(static fn (NotFound $e) => new JsonResponse(
            ['message' => DomainErrorMessage::for($e)],
            Response::HTTP_NOT_FOUND,
        ));
        $handler->renderable(static fn (DomainError $e) => new JsonResponse(
            ['message' => DomainErrorMessage::for($e)],
            Response::HTTP_UNPROCESSABLE_ENTITY,
        ));
    }
}
