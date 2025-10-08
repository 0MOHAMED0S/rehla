<?php

namespace App\Http\Controllers\Admin\Subscribers;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    public function index()
    {
        $subscribers = Subscriber::with(['user', 'shipping', 'subscribeDetail'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'All subscribers retrieved successfully',
            'data'    => $subscribers,
        ]);
    }
}
