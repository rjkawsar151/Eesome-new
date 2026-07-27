<?php

namespace App\Http\Middleware;

use App\Services\AdminPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function __construct(private AdminPermissionService $permissions) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Access denied.');
        }

        $permission = $this->permissions->permissionForRoute($request->route()?->getName());
        if (! $this->permissions->allows($user, $permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
