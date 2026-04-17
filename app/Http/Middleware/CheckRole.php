<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {
        if(!Auth::guard('api')->check()){
            return response()->json([
                'message' => 'Unauthenticated. Please log in.'], 401);
        }

        $user=Auth::guard('api')->user();
        if(!in_array($user->role,$roles)){
            return response()->json([
                'message' => 'Forbidden access. Your role ('.$user->role.') does not have permission to access this resource.'
            ],403);
        }
        return $next($request);
    }
}
