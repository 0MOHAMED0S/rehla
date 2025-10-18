<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstructorRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! $request->user() ||
            ($request->user()->role->name !== 'instructor')
        ) {
            return response()->json([
                'status'  => false,
                'message' => 'تم رفض الوصول. هذه الصفحة تتطلب صلاحية  مدرب .'
            ], 403);
        }
        return $next($request);
    }
}
