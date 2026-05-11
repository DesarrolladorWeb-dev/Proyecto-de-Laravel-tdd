<?php

// use Dotenv\Exception\ValidationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php', // quitamos no lo necesitamos
        api: __DIR__.'/../routes/api.php',
        // Aqui Agregamos nuestra ruta ver rutas php artisan route:list
        apiPrefix: 'api/v1',
        // commands: __DIR__.'/../routes/console.php', 
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(\Illuminate\Validation\ValidationException $throwable) {
            // dd('aqui');
            // de esta manera agregamos esto a la respuesta 
            return jsonResponse(status: 422, message: $throwable->getMessage(),  errors: $throwable->errors());
        });

    })->create();
