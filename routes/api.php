<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ConvertController;
use App\Http\Controllers\Api\V1\RatesController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

Route::group([
    'prefix' => 'v1/auth'
], static function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
})->middleware([Authenticate::class, 'auth:api']);

Route::group([
    'prefix' => 'v1/'
], static function () {
    $method = Request::capture()->collect('method')->join('');
    Route::get('', static function () use ($method) {
        if ('rates' === $method) {
            return app(RatesController::class)();
        }
        throw new NotFoundHttpException();
    })->middleware([Authenticate::class, 'auth:api']);

    Route::post('', static function () use ($method) {
        if ('convert' === $method) {
            return app(ConvertController::class)();
        }
        throw new NotFoundHttpException();
    })->middleware([Authenticate::class, 'auth:api']);
});
