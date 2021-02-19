<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HasPremium
{
    /**
     * Check for premium status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user('sanctum');
        if ($user->premium != 1) return response()->json([
            'message' => 'Премиум не активирован',
            'status' => 'error'
        ], 403);
        return $next($request);
    }
}
