<?php

namespace App\Http\Controllers\Admin\EnhaLak;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeDetails\UpdateSubscribeDetailsRequest;
use App\Models\SubscribeDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscribeDetailController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $subscribe = SubscribeDetails::first();

            if (! $subscribe) {
                return response()->json([
                    'status'  => false,
                    'message' => 'لا يوجد بيانات اشتراك حالياً',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data'   => $subscribe,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب بيانات الاشتراك',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    public function update(UpdateSubscribeDetailsRequest $request): JsonResponse
    {
        try {
            $subscribe = SubscribeDetails::first();

            if (! $subscribe) {
                return response()->json([
                    'status'  => false,
                    'message' => 'لا يوجد أي بيانات اشتراك لتحديثها',
                ], 404);
            }

            $subscribe->update($request->validated());

            return response()->json([
                'status'  => true,
                'message' => 'تم تعديل بيانات الاشتراك بنجاح',
                'data'    => $subscribe,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء تعديل بيانات الاشتراك',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
