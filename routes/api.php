<?php

use App\Http\Controllers\Admin\AboutUs\AboutUsController;
use App\Http\Controllers\Admin\BedaetElrehla\PackageController;
use App\Http\Controllers\Admin\ContentCreator\ArticleController;
use App\Http\Controllers\Admin\EnhaLak\OrdersController;
use App\Http\Controllers\Admin\EnhaLak\SubscribeDetailController;
use App\Http\Controllers\Admin\Users\RolesController;
use App\Http\Controllers\Admin\Users\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\Articles\ArticleController as UserArticleController;
use App\Http\Controllers\Admin\Contacts\ContactController;
use App\Http\Controllers\Admin\EnhaLak\ProducController;
use App\Http\Controllers\Admin\LogoAndLink\LogoAndLinkController;
use App\Http\Controllers\Admin\Shipping\ShippingController;
use App\Http\Controllers\Admin\Subscribers\SubscribeController as SubscribersSubscribeController;
use App\Http\Controllers\Admin\TermsOfUse\TermsOfUseController;
use App\Http\Controllers\Admin\Trainers\TrainerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackageOrderController;
use App\Http\Controllers\PriceEquationController;
use App\Http\Controllers\TrainerScheduleController;
use App\Http\Controllers\User\Child\ChildController;
use App\Http\Controllers\User\Contacts\ContactController as UserContactController;
use App\Http\Controllers\User\LogoAndLink\LogoAndLinkController as UserLogoAndLinkController;
use App\Http\Controllers\User\Orders\OrderController as UserOrderController;
use App\Http\Controllers\User\Products\ProductController  as UserProductController;
use App\Http\Controllers\User\Subscribers\SubscribeController;
use App\Http\Controllers\UserPackageController;
use App\Models\TrainerSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/profile', function (Request $request) {
    $user = $request->user()->load('role');

    return response()->json([
        'user' => $user
    ]);
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
    Route::post('user/subscriber', [SubscribeController::class, 'store']);
    Route::get('user/my-subscription', [SubscribeController::class, 'getMySubscriptions']);

    Route::post('user/package-orders', [UserPackageController::class, 'store']);

});

Route::post('user/subscriber/callback', [SubscribeController::class, 'callback']);

//Author
Route::middleware(['auth:sanctum', 'author'])->group(function () {
    Route::apiResource('articles', ArticleController::class); //done
});
//support
Route::middleware(['auth:sanctum', 'support'])->group(function () {
    Route::get('/admin/contact', [ContactController::class, 'index']);//done
    Route::post('/admin/contact/{id}', [ContactController::class, 'show']);//done
});


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('admin/logos-and-links', [LogoAndLinkController::class, 'update']);//done

    Route::apiResource('users', UsersController::class);
    Route::Put('/users/{id}/role', [RolesController::class, 'updateUserRole']);
    Route::get('/roles', [RolesController::class, 'index']);
    Route::put('/admin/aboutus/update', [AboutUsController::class, 'update']);
    Route::put('/admin/terms/update', [TermsOfUseController::class, 'update']);
    Route::get('/admin/orders', [OrdersController::class, 'index']);
    Route::Put('/admin/orders/{order_id}/note', [OrdersController::class, 'updateNote']);
    Route::get('admin/price-equation', [PriceEquationController::class, 'index']);
    Route::put('admin/price-equation', [PriceEquationController::class, 'update']);

    Route::get('admin/package-orders', [PackageOrderController::class, 'index']);
    Route::get('admin/package-orders/{id}', [PackageOrderController::class, 'show']);

    Route::get('admin/dashboard', [DashboardController::class, 'index']);

});

Route::middleware('auth:sanctum')->get('/user/package-orders', [PackageOrderController::class, 'myOrders']);
Route::middleware('auth:sanctum')->get('/child/package-orders', [PackageOrderController::class, 'myPackageOrdersForChild']);
Route::middleware('auth:sanctum')->get('/child/package-orders/{id}', [PackageOrderController::class, 'showChildOrder']);



Route::middleware(['auth:sanctum', 'enhalak'])->group(function () {
    Route::put('/admin/shipping/{id}', [ShippingController::class, 'update']);//done
    Route::post('/admin/shipping', [ShippingController::class, 'store']);//done
    Route::delete('/admin/shipping/{id}', [ShippingController::class, 'destroy']);//done
    Route::apiResource('/admin/products', ProducController::class);//done
    Route::put('admin/subscribe-detail', [SubscribeDetailController::class, 'update']);
    Route::get('admin/orders/{id}', [OrdersController::class, 'showOrderDetails']);
    Route::get('admin/subscribers', [SubscribersSubscribeController::class, 'index']);
    Route::get('admin/subscribe-detail', [SubscribeDetailController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'bedaet'])->group(function () {
    Route::apiResource('/admin/packages', PackageController::class);
    Route::get('/admin/trainer-schedules/pending', [TrainerScheduleController::class, 'pending']);
    Route::put('/admin/trainer-schedules/{id}', [TrainerScheduleController::class, 'update']);
    Route::put('/admin/trainers/{id}', [TrainerController::class, 'update']);
    Route::post('admin/trainers', [TrainerController::class, 'store']);
    Route::get('admin/trainers', [TrainerController::class, 'index']);
    Route::get('admin/trainers/all', [TrainerScheduleController::class, 'all']);
});

Route::middleware(['auth:sanctum', 'instructor'])->group(function () {
    Route::post('trainer/schedule', [TrainerScheduleController::class, 'store']);
    Route::get('trainer/schedule/my', [TrainerScheduleController::class, 'mySchedules']);
    Route::get('/trainer/profile', [TrainerScheduleController::class, 'profile']);
    Route::get('/trainer/dashboard', [DashboardController::class, 'instructorStats']);
});



//articles
Route::get('user/articles', [UserArticleController::class, 'index']); //done
Route::get('user/articles/{slug}', [UserArticleController::class, 'show']);///done

//contact
Route::get('/user/contact-subjects', [UserContactController::class, 'index']);//done
Route::post('/user/contact', [UserContactController::class, 'store']);//done

//shipping
Route::get('/user/shipping', [ShippingController::class, 'index']);//done

//logosandlinks
Route::get('user/logos-and-links', [UserLogoAndLinkController::class, 'index']);//done

//products
Route::get('/user/products', [UserProductController::class, 'index']);//done
Route::get('/user/products/{id}', [UserProductController::class, 'show']);//done












Route::middleware(['auth:sanctum', 'check.child'])->get('user/children/{child}', [ChildController::class, 'childDetails']);


Route::get('/user/aboutus', [AboutUsController::class, 'index']);
Route::get('/user/terms', [TermsOfUseController::class, 'index']);

Route::middleware(['auth:sanctum'])->post('/user/order/{id}', [UserOrderController::class, 'store']);
Route::post('/paymob/webhook', [UserOrderController::class, 'callback']);




Route::get('/trainers', [TrainerController::class, 'index2']);
Route::get('/trainers/{id}', [TrainerController::class, 'show']);




Route::get('/package/trainers/{packageId}', [UserPackageController::class, 'getTrainersByPackage']);
Route::get('/package/trainers/{id}/schedules', [UserPackageController::class, 'getTrainerSchedules']);


//packages
Route::get('package/search/trainers', [UserPackageController::class, 'searchTrainers']);
Route::get('/packages', [UserPackageController::class, 'index']);
Route::get('user/trainer-schedules/approved', [TrainerScheduleController::class, 'approved']);

