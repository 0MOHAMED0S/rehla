<?php

namespace App\Http\Controllers\Admin\TermsOfUse;

use App\Http\Controllers\Controller;
use App\Http\Requests\TermsOfUse\TermsOfUseRequest;
use App\Models\TermsOfUse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TermsOfUseController extends Controller
{
    public function update(TermsOfUseRequest $request): JsonResponse
    {
        try {
            $terms = TermsOfUse::updateOrCreate(
                ['id' => 1],
                $request->validated()
            );

            return response()->json([
                'status'  => true,
                'message' => 'تم تحديث شروط الاستخدام بنجاح.',
                'data'    => $terms,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في تحديث شروط الاستخدام.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

public function index(): JsonResponse
{
    try {
        $terms = TermsOfUse::first();

        if (!$terms) {
            return response()->json([
                'status'  => false,
                'message' => 'لم يتم العثور على الشروط والأحكام',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $terms,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'حدث خطأ أثناء جلب البيانات',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
}
