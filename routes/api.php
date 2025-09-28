<?php

use App\Http\Controllers\Admin\ContentCreator\ArticleController;
use App\Http\Controllers\Admin\Users\RolesController;
use App\Http\Controllers\Admin\Users\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\Articles\ArticleController as UserArticleController;
use App\Http\Controllers\Admin\Contacts\ContactController;
use App\Http\Controllers\Admin\EnhaLak\ProducController;
use App\Http\Controllers\Admin\LogoAndLink\LogoAndLinkController;
use App\Http\Controllers\Admin\Shipping\ShippingController;
use App\Http\Controllers\User\Child\ChildController;
use App\Http\Controllers\User\Contacts\ContactController as UserContactController;
use App\Http\Controllers\User\LogoAndLink\LogoAndLinkController as UserLogoAndLinkController;
use App\Http\Controllers\User\Products\ProductController  as UserProductController;
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
    Route::post('/user/children', [ChildController::class, 'store']);
    Route::get('/user/children/check', [ChildController::class, 'checkChildren']);


});

//Author
Route::middleware(['auth:sanctum', 'author'])->group(function () {
    Route::apiResource('articles', ArticleController::class);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('admin/logos-and-links', [LogoAndLinkController::class, 'update']);
    Route::apiResource('users', UsersController::class);
    Route::Put('/users/{id}/role', [RolesController::class, 'updateUserRole']);
    Route::get('/roles', [RolesController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'support'])->group(function () {
    Route::get('/admin/contact', [ContactController::class, 'index']);
    Route::post('/admin/contact/{id}', [ContactController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'enhalak'])->group(function () {
        Route::put('/admin/shipping/{id}', [ShippingController::class, 'update']);
});
        Route::apiResource('/admin/products', ProducController::class);

//articles
Route::get('user/articles', [UserArticleController::class, 'index']);
Route::get('user/articles/{slug}', [UserArticleController::class, 'show']);

//contact
Route::get('/user/contact-subjects', [UserContactController::class, 'index']);
Route::post('/user/contact', [UserContactController::class, 'store']);

//shipping
Route::get('/user/shipping', [ShippingController::class, 'index']);

//logosandlinks
Route::get('user/logos-and-links', [UserLogoAndLinkController::class, 'index']);

//products
Route::get('/user/products', [UserProductController::class, 'index']);
Route::get('/user/products/{id}', [UserProductController::class, 'show']);


Route::middleware(['auth:sanctum', 'check.child'])->get('user/children/{child}', [ChildController::class, 'childDetails']);

