<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// Grants access to POS Supervisors and Super Admins only.
// Blocks regular Cashiers from supervisor-only pages.
class AuthSupervisor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->utype === 'ADM' || $user->isSupervisor()) {
            return $next($request);
        }

        abort(403, 'Supervisor access required.');
    }
}
