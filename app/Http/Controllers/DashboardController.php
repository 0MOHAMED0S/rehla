<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\Article;
use App\Models\PackageOrder;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // Total orders ('إنها لك')
        $totalOrders = Order::count();

        // Active subscriptions
        $activeSubscriptions = Subscriber::where('status', 'active')->count();

        // Total products ('إنها لك')
        $totalProducts = Product::count();

        // Total writing package orders
        $totalPackageOrders = PackageOrder::count();

        // Total students (الطلب)
        $totalStudents = User::whereHas('role', fn($q) => $q->where('name', 'student'))->count();

        // Total trainers
        $totalTrainers = User::whereHas('role', fn($q) => $q->where('name', 'trainer'))->count();

        // Total published articles
        $publishedArticles = Article::where('status', 1)->count();

        // Total articles (overall)
        $totalArticles = Article::count();

        // New join requests (trainers pending)
        $joinRequests = User::whereHas('role', fn($q) => $q->where('name', 'trainer'))
                            ->whereDoesntHave('trainerProfile')
                            ->count();

        // Total users
        $totalUsers = User::count();

        // New support messages (unread)
        $newSupportMessages = ContactMessage::where('is_read', false)->count();

        // Total completed revenues (from orders + packages)
        $completedRevenue = (float) Order::where('status', 'completed')->sum('price')
            + (float) PackageOrder::where('status', 'completed')->sum('price');

        return response()->json([
            'status' => true,
            'message' => 'تم جلب بيانات لوحة التحكم بنجاح',
            'data' => [
                'total_orders' => $totalOrders,
                'active_subscriptions' => $activeSubscriptions,
                'total_products' => $totalProducts,
                'total_package_orders' => $totalPackageOrders,
                'total_students' => $totalStudents,
                'total_trainers' => $totalTrainers,
                'published_articles' => $publishedArticles,
                'total_articles' => $totalArticles,
                'join_requests' => $joinRequests,
                'total_users' => $totalUsers,
                'new_support_messages' => $newSupportMessages,
                'completed_revenue' => $completedRevenue,
            ]
        ]);
    }
}
