<?php

namespace App\Http\Controllers\Admin\LogoAndLink;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogoAndLinks\LogoAndLinkRequest;
use App\Models\LogoAndLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogoAndLinkController extends Controller
{
    public function update(LogoAndLinkRequest $request): JsonResponse
    {
        try {
            $logoLink = LogoAndLink::first();

            $data = $request->validated();

            foreach (['main_logo', 'creative_writing_logo', 'gate_inha_lak_image', 'gate_start_journey_image', 'about_page_image'] as $field) {
                if ($request->hasFile($field)) {
                    if ($logoLink && $logoLink->$field && Storage::disk('public')->exists($logoLink->$field)) {
                        Storage::disk('public')->delete($logoLink->$field);
                    }
                    $data[$field] = $request->file($field)->store('logos', 'public');
                }
            }

            if ($logoLink) {
                $logoLink->update($data);
            } else {
                $logoLink = LogoAndLink::create($data);
            }

            return response()->json([
                'status'  => true,
                'message' => 'تم تحديث الشعارات والروابط بنجاح',
                'data'    => $logoLink,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء تحديث البيانات',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
