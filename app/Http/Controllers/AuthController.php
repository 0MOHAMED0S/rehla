<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $defaultRole = Role::where('name', 'user')->first();

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $defaultRole->id,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => 'تم تسجيل المستخدم بنجاح',
                'user'    => $user->load('role'), // تضمين علاقة الدور
                'token'   => $token
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في تسجيل المستخدم',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status'  => false,
                    'message' => 'بيانات الدخول غير صحيحة'
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'user'    => $user->load('role'), // تضمين علاقة الدور
                'token'   => $token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في تسجيل الدخول',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function logout(): JsonResponse
    {
        try {
            auth()->user()->tokens()->delete();

            return response()->json([
                'status'  => true,
                'message' => 'تم تسجيل الخروج بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في تسجيل الخروج',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
