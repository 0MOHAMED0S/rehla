<?php

namespace App\Http\Controllers\User\Child;

use App\Http\Controllers\Controller;
use App\Http\Requests\Child\StoreChildRequest;
use App\Models\Child;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ChildController extends Controller
{
    public function store(StoreChildRequest $request)
    {
        DB::beginTransaction();

        try {
            $parentId = Auth::id();
            $childUser = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => Role::where('name', 'student')->first()->id,
            ]);

            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            $childDetails = Child::create([
                'user_id'   => $childUser->id,
                'parent_id' => $parentId,
                'age'       => $request->age,
                'gender'    => $request->gender,
                'interests' => $request->interests,
                'strengths' => $request->strengths,
                'avatar'    => $avatarPath,
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'تم إضافة الطفل بنجاح',
                'child'   => $childDetails,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'فشل في إضافة الطفل',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    public function childDetails($childId)
    {
        try {
            $child = Child::findOrFail($childId);

            // Merge child data and user data into one array
            $childData = array_merge(
                $child->toArray(),
                $child->user ? $child->user->toArray() : []
            );

            return response()->json([
                'status'  => true,
                'message' => 'تم جلب بيانات الطفل بنجاح',
                'child'   => $childData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب بيانات الطفل',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function checkChildren()
    {
        try {
            $parentId = Auth::id();

            // جلب الأطفال مع بيانات المستخدم المرتبطة لتجنّب N+1
            $children = Child::with('user')
                ->where('parent_id', $parentId)
                ->get();

            // تحويل كل سجل لإخراج منظم (child + nested user)
            $childrenData = $children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'user_id' => $child->user_id,
                    'parent_id' => $child->parent_id,
                    'age' => $child->age,
                    'gender' => $child->gender,
                    'interests' => $child->interests,
                    'strengths' => $child->strengths,
                    'avatar' => $child->avatar ? asset('storage/' . $child->avatar) : null, // اختياري: رابط كامل
                    // nested user info (if exists)
                    'user' => $child->user ? [
                        'id' => $child->user->id,
                        'name' => $child->user->name,
                        'email' => $child->user->email,
                        // أضف حقولا أخرى حسب حاجتك، تجنّب الحقول الحساسة
                    ] : null,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => $children->isNotEmpty() ? 'لدى المستخدم أطفال مسجلين' : 'لا يوجد أطفال للمستخدم',
                'has_children' => $children->isNotEmpty(),
                'children' => $childrenData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('checkChildren error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب الأطفال.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
