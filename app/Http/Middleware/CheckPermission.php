<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admin has full access to every route and resource
        if ($user->esAdmin()) {
            return $next($request);
        }

        // Check if user has at least one of the passed permissions
        foreach ($permissions as $perm) {
            $codes = explode('|', $perm);
            foreach ($codes as $code) {
                if ($user->tienePermiso(trim($code))) {
                    return $next($request);
                }
            }
        }

        abort(403, 'No tienes permisos suficientes para realizar esta acción o acceder a esta sección.');
    }
}
