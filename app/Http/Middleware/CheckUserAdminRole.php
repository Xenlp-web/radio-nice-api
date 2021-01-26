<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user('sanctum');
        if ($user->role != 'admin') return response()->json([
            'message' => 'У вас нет прав администратора',
            'status' => 'error'
        ], 400);
        return $next($request);
    }
}
