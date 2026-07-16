<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $allowedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $item) {
                $allowedRoles[] = trim($item);
            }
        }

        if (! auth()->check() || ! in_array(auth()->user()->role, $allowedRoles)) {
            if (auth()->check()) {
                if (in_array(auth()->user()->role, ['admin', 'pembimbing'])) {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('intern.absensi');
            }

            return redirect('/');
        }

        return $next($request);
    }
}
