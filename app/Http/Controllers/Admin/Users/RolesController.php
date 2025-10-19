<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::all();

            return response()->json([
                'status' => true,
                'message' => 'تم جلب الأدوار بنجاح',
                'roles' => $roles
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'فشل في جلب الأدوار',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // public function updateUserRole(Request $request, $id)
    // {
    //     try {
    //         $user = User::find($id);

    //         if (! $user) {
    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'المستخدم غير موجود',
    //             ], 404);
    //         }

    //         $validated = $request->validate([
    //             'role_id' => 'required|exists:roles,id',
    //         ], [
    //             'role_id.required' => 'الدور مطلوب',
    //             'role_id.exists'   => 'الدور غير موجود في قاعدة البيانات',
    //         ]);

    //         $user->update([
    //             'role_id' => $validated['role_id'],
    //         ]);

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'تم تحديث دور المستخدم بنجاح',
    //             'user'    => $user->load('role')
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'فشل في تحديث دور المستخدم',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function updateUserRole(Request $request, $id)
{
    try {
        $authUser = auth()->user(); // current logged-in user
        $user = User::with(['role', 'trainerProfile', 'childProfile'])->find($id);

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => 'المستخدم غير موجود',
            ], 404);
        }

        // ✅ Validation
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ], [
            'role_id.required' => 'الدور مطلوب',
            'role_id.exists'   => 'الدور غير موجود في قاعدة البيانات',
        ]);

        $newRoleId = $validated['role_id'];

        // ✅ Get the names of roles for logic comparison
        $newRoleName  = Role::find($newRoleId)?->name;
        $currentRole  = $user->role?->name;
        $authRoleName = $authUser->role?->name;

        // ✅ 1. Prevent last Super Admin from changing himself
        if ($currentRole === 'superadmin' && $authUser->id === $user->id) {
            $superadminCount = User::whereHas('role', fn($q) => $q->where('name', 'superadmin'))->count();
            if ($superadminCount === 1) {
                return response()->json([
                    'status'  => false,
                    'message' => 'لا يمكنك تغيير دورك لأنك آخر مدير نظام (Super Admin).',
                ], 403);
            }
        }

        // ✅ 2. Prevent changing to instructor if user has no trainer profile
if ($newRoleName === 'instructor' && ! $user->trainerProfile) {
    return response()->json([
        'status'  => false,
        'message' => 'لا يمكن تحويل المستخدم إلى مدرب لأنه لا يملك ملف مدرب.',
    ], 422);
}


        // ✅ 3. Prevent assigning "child" role to a user who already has children or linked trainer
        if ($newRoleName === 'child' && $user->childProfile) {
            return response()->json([
                'status'  => false,
                'message' => 'لا يمكن تحويل هذا المستخدم إلى طفل لأنه مرتبط بملف طفل.',
            ], 422);
        }

        // ✅ 4. Update role safely
        $user->update(['role_id' => $newRoleId]);

        return response()->json([
            'status'  => true,
            'message' => 'تم تحديث دور المستخدم بنجاح',
            'user'    => $user->load('role', 'trainerProfile', 'childProfile')
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'حدث خطأ أثناء تحديث دور المستخدم',
            'error'   => $e->getMessage()
        ], 500);
    }
}

}
