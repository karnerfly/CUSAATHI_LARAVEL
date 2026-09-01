<?php

use App\Http\Middleware\CheckSessionRevoked;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api/v2',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->alias([
            'session.revoked' => CheckSessionRevoked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn(Request $request) => $request->is('api/*') || $request->expectsJson());
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            $previous = $e->getPrevious();
            if ($previous instanceof ModelNotFoundException) {
                $model_class = $previous->getModel();
                $model_name = class_basename($model_class);
                $ids = $previous->getIds();
                $id_string = implode(', ', $ids);

                $message = !empty($ids) ? "No {$model_name} found with ID [{$id_string}]." : "No {$model_name} found.";
                return response()->json(
                    [
                        'message' => $message,
                    ],
                    404,
                );
            }

            return null;
        });
    })
    ->create();
