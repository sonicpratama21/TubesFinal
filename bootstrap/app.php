<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tamu yang membuka halaman terproteksi diarahkan ke landing page dan modal login terbuka
        $middleware->redirectGuestsTo('/?login=1');
        $middleware->alias(['admin' => EnsureUserIsAdmin::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*', 'reservasi*', 'apiUsers', 'auth/*') || $request->expectsJson(),
        );
    })->create();
