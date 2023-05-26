<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsApiController;


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
    Route::get('/articles/search', [NewsApiController::class, 'search'])->name('articles.search');
    Route::post('/articles/preferences', [NewsApiController::class, 'preferences'])->name('articles.preferences');
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
