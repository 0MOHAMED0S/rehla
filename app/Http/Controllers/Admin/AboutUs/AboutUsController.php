<?php

namespace App\Http\Controllers\admin\AboutUs;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutUs\UpdateAboutUsRequest;
use App\Models\AboutUs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $about = AboutUs::first();

            if (!$about) {
                return response()->json([
                    'status'  => false,
                    'message' => 'لا توجد بيانات متاحة حالياً',
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'تم جلب بيانات من نحن بنجاح',
                'data'    => $about,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateAboutUsRequest $request): JsonResponse
    {
        try {
            $about = AboutUs::first();

            if (!$about) {
                return response()->json([
                    'status'  => false,
                    'message' => 'لا توجد بيانات لتحديثها',
                ], 404);
            }

            $about->update($request->validated());

            return response()->json([
                'status'  => true,
                'message' => 'تم تحديث بيانات من نحن بنجاح',
                'data'    => $about,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء تحديث البيانات',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
