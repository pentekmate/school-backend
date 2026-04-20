<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->statefulApi();

        $middleware->validateCsrfTokens(except: [
        '/api/*', // Opcionális: ha bizonyos api végpontoknál nem kell CSRF
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->withExceptions(function (Exceptions $exceptions) {

        // Itt kapjuk el globálisan a "Nincs találat" hibát
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'A kért adat nem található az adatbázisban.',
                ], 404);
            }
        });

    })
    ->create();
