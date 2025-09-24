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
    public function updateUserRole(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'المستخدم غير موجود',
                ], 404);
            }

            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id',
            ], [
                'role_id.required' => 'الدور مطلوب',
                'role_id.exists'   => 'الدور غير موجود في قاعدة البيانات',
            ]);

            $user->update([
                'role_id' => $validated['role_id'],
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'تم تحديث دور المستخدم بنجاح',
                'user'    => $user->load('role')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في تحديث دور المستخدم',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
