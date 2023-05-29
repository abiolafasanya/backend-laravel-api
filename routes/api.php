<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsApiController;
use App\Http\Controllers\OtherNewsController;
use App\Http\Controllers\RadpidNewsController;


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
    Route::get('/articles', [NewsApiController::class, 'index'])->name('articles.index');
    Route::get('/nytimes', [OtherNewsController::class, 'index'])->name('others.index');
    Route::get('/guardians', [OtherNewsController::class, 'guardians'])->name('others.guardians');
    Route::get('/test', [OtherNewsController::class, 'getGuardianArticles'])->name('others.test');
    Route::get('/articles/search', [NewsApiController::class, 'search'])->name('articles.search');
    Route::post('/articles/preferences', [NewsApiController::class, 'preferences'])->name('articles.preferences');
    Route::get('/articles/headlines', [NewsApiController::class, 'headlines'])->name('articles.headlines');
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
