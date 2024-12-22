<?php

namespace App\Http\Middleware;

use Closure;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (!auth()->user()->hasPermission($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
