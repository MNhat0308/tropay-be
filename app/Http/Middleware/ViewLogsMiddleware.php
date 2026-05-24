<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewLogsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->hasPermissionTo('view_logs_user')) {
            return $next($request);
        }

        abort(401, 'Unauthorised');
    }
}
