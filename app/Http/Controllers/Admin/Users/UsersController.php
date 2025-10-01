<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    public function index()
    {
        try {
            $users = User::with(['role', 'children.childUser:id,name'])->paginate(10);

            return response()->json([
                'status'  => true,
                'message' => 'تم جلب المستخدمين بنجاح',
                'users'   => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في جلب المستخدمين',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    public function store(RegisterRequest $request)
    {
        try {
            $defaultRole = Role::where('name', 'user')->first();

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $defaultRole->id ?? 2,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'تم إضافة المستخدم بنجاح',
                'user'    => $user->load('role')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في إضافة المستخدم',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with([
                'role',
                'orders' => function ($query) {
                    $query->select('id', 'user_id', 'product_id', 'price', 'status', 'created_at')
                        ->with(['product:id,name']);
                }
            ])->find($id);

            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'المستخدم غير موجود',
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'تم جلب بيانات المستخدم بنجاح',
                'user'    => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في جلب بيانات المستخدم',
                'error'   => $e->getMessage()
            ], 500);
        }
    }




    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::find($id);

            if (! $user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'المستخدم غير موجود',
                ], 404);
            }

            $data = $request->only(['name', 'email']);

            $user->update($data);

            return response()->json([
                'status'  => true,
                'message' => 'تم تحديث بيانات المستخدم بنجاح',
                'user'    => $user->load('role')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في تحديث بيانات المستخدم',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user) {}
}
