<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!auth()->check()) {
            abort(403, 'Maaf, Anda harus login terlebih dahulu.');
        }

        $permissions = explode('|', $permission);
        foreach ($permissions as $p) {
            if (auth()->user()->hasPermission($p)) {
                return $next($request);
            }
        }

        abort(403, 'Maaf, Anda tidak memiliki akses yang cukup untuk fitur ini.');
    }
}
