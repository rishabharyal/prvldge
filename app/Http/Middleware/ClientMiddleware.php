<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $agent = $request->server('HTTP_USER_AGENT');
        if ($agent === 'Memory Test' || $agent === 'Memory App') {
            return $next($request);
        }

        return response()->json(['success' => false, 'status' => 'NOT_ALLOWED' . $request->server('HTTP_USER_AGENT')]);
    }
}
