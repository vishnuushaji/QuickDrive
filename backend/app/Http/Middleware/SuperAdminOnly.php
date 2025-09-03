<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Only super admin can access this');
        }

        return $next($request);
    }
}