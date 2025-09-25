<?php

namespace App\Http\Controllers\Admin\Contacts;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $messages = ContactMessage::with('subject')
                ->latest()
                ->paginate(10);

            return response()->json([
                'status'   => true,
                'message'  => 'تم جلب الرسائل بنجاح',
                'messages' => $messages->through(function ($msg) {
                    return [
                        'id'      => $msg->id,
                        'name'    => $msg->name,
                        'email'   => $msg->email,
                        'message' => $msg->message,
                        'subject' => $msg->subject ? $msg->subject->name : null,
                        'created_at' => $msg->created_at->toDateTimeString(),
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب الرسائل',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    public function show($id): JsonResponse
    {
        try {
            $message = ContactMessage::with('subject')->find($id);

            if (! $message) {
                return response()->json([
                    'status'  => false,
                    'message' => 'الرسالة غير موجودة',
                ], 404);
            }

            if ($message->is_read == 0) {
                $message->update(['is_read' => 1]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'تم جلب تفاصيل الرسالة بنجاح',
                'data'    => [
                    'id'          => $message->id,
                    'name'        => $message->name,
                    'email'       => $message->email,
                    'message'     => $message->message,
                    'subject'     => $message->subject ? $message->subject->name : null,
                    'is_read'     => $message->is_read,
                    'created_at'  => $message->created_at->toDateTimeString(),
                    'updated_at'  => $message->updated_at->toDateTimeString(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب تفاصيل الرسالة',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
