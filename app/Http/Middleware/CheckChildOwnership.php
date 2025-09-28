<?php

namespace App\Http\Middleware;

use App\Models\Child;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckChildOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $childId = $request->route('child');

        $child = Child::find($childId);

        if (!$child) {
            return response()->json([
                'status'  => false,
                'message' => 'الطفل غير موجود'
            ], 404);
        }

        $user = Auth::user();

        // Allow if user is parent OR super admin
        if ($child->parent_id === $user->id || $user->role === 1) {
            return $next($request);
        }

        return response()->json([
            'status'  => false,
            'message' => 'ليس لديك إذن لعرض هذا الملف الشخصي'
        ], 403);
    }
}
