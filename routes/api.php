<?php

use App\Http\Controllers\Admin\ContentCreator\ArticleController;
use App\Http\Controllers\Admin\Users\RolesController;
use App\Http\Controllers\Admin\Users\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\Articles\ArticleController as UserArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/profile', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes for guests (not authenticated)
Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Routes for authenticated users (Sanctum token required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

//Author
Route::middleware(['auth:sanctum', 'author'])->group(function () {
    Route::apiResource('articles', ArticleController::class);
});

Route::middleware(['auth:sanctum','admin'])->group(function () {
    Route::apiResource('users', UsersController::class);
    Route::Put('/users/{id}/role', [RolesController::class, 'updateUserRole']);
    Route::get('/roles', [RolesController::class, 'index']);
});
    Route::get('user/articles', [UserArticleController::class, 'index']);
    Route::get('user/articles/{slug}', [UserArticleController::class, 'show']);
