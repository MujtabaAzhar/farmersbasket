<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// Grants access to POS Supervisors and Cashiers.
// Super Admin (utype=ADM) also gets POS access so they can test/monitor.
class AuthPOS
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->utype === 'ADM' || $user->isPosUser()) {
            return $next($request);
        }

        abort(403, 'POS access required.');
    }
}
