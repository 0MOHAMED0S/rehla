<?php

namespace App\Http\Controllers\User\LogoAndLink;

use App\Http\Controllers\Controller;
use App\Models\LogoAndLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoAndLinkController extends Controller
{
    public function index(): JsonResponse
{
    try {
        $data = LogoAndLink::first();

        if (!$data) {
            return response()->json([
                'status'  => false,
                'message' => 'لا توجد بيانات حالياً.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $data,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'حدث خطأ أثناء جلب البيانات.',
            'error'   => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}

}
