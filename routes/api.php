<?php

use App\Http\Controllers\API\BusinessApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\NewsApiController;
use App\Http\Controllers\API\PoliticsController;
use App\Http\Controllers\API\SearchApiController;
use App\Http\Controllers\API\SportApiController;
use App\Http\Controllers\API\TechApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('v1')->group(function () {

    Route::post('/auth/signup', [AuthController::class, 'register']);
    Route::post('/auth', [AuthController::class, 'login']);

    Route::get('/csrf-token', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    });

    Route::get('/articles', [NewsApiController::class, 'index']);
    Route::get('/articles/headlines', [NewsApiController::class, 'headlines']);
    Route::get('/articles/sources', [NewsApiController::class, 'getSources']);

    Route::get('/articles/politics', [PoliticsController::class, 'index']);
    Route::get('/articles/sports', [SportApiController::class, 'index']);
    Route::get('/articles/tech', [TechApiController::class, 'index']);
    Route::get('/articles/business', [BusinessApiController::class, 'index']);
    Route::get('/articles/search', [SearchApiController::class, 'search']);

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/preferences', [UserController::class, 'userPreferences']);
    });
});
