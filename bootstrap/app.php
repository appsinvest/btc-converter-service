<?php

use App\Exceptions\ApplicationException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedHttpException;
use App\Http\Middleware\AuthTokenPreProcess;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(AuthTokenPreProcess::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (UnauthorizedHttpException $e, $request) {
            if ($request->is(['api/v1/*', 'api/v1', ])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'code' => \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED
                ], \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
            }
        });

        $exceptions->render(function (ApplicationException|Error $e, $request) {
            if ($request->is(['api/v1/*', 'api/v1', ])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token',
                    'code' => \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN
                ], \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
            }
        });

        $exceptions->render(function (ForbiddenException $e, $request) {
            if ($request->is(['api/v1/*', 'api/v1', ])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token',
                    'code' => \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN
                ], \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/v1')) {
                return response()->json([
                    'status' => 'error',
                    'message' => sprintf('Route "%s" Not found', $request->getRequestUri()),
                    'code' => \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
                ], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
            }
        });

        $exceptions->render(function (RouteNotFoundException $e, $request) {
            if ($request->is('api/v1')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token',
                    'code' => \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN
                ], \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
            }
        });
    })
    ->create();
