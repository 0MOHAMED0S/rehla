<?php

namespace App\Http\Controllers\User\Contacts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contacts\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\ContactSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $subjects = ContactSubject::all(['id', 'name']);
            return response()->json([
                'status'   => true,
                'message'  => 'تم جلب المواضيع بنجاح',
                'subjects' => $subjects,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب المواضيع',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $message = ContactMessage::create($data);

            return response()->json([
                'status'  => true,
                'message' => 'تم إرسال رسالتك بنجاح',
                'data'    => $message,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
