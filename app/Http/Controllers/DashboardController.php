<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\Article;
use App\Models\PackageOrder;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
        $completedRevenue = (float) Order::where('status', 'paid')->sum('price')
            + (float) PackageOrder::where('status', 'ongoing')->sum('price');

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
    public function instructorStats()
    {
        $trainer = Auth::user();

        // إجمالي الجلسات (الطلبات)
        $totalSessions = PackageOrder::where('trainer_id', $trainer->id)->count();

        // الجلسات المكتملة
        $completedSessions = PackageOrder::where('trainer_id', $trainer->id)
            ->where('status', 'completed')
            ->count();

        // إجمالي الطلاب (عدد الأطفال الفريدين)
        $totalStudents = PackageOrder::where('trainer_id', $trainer->id)
            ->distinct('child_id')
            ->count('child_id');

        // // متوسط التقييم (يفترض أن عندك جدول تقييم مثلاً trainer_reviews)
        // $averageRating = \App\Models\TrainerReview::where('trainer_id', $trainer->id)->avg('rating') ?? 0;

        return response()->json([
            'status' => true,
            'data' => [
                // 'average_rating'     => round($averageRating, 1),
                'total_students'     => $totalStudents,
                'completed_sessions' => $completedSessions,
                'total_sessions'     => $totalSessions,
            ],
        ]);
    }
}
