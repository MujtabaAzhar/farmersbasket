<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginActivityLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // Redirect POS users to /pos, admins to /admin, others to /
    protected function authenticated(Request $request, $user)
    {
        LoginActivityLog::log('login', $user->email, $user->id);

        if ($user->utype === 'ADM') {
            return redirect()->route('admin.index');
        }

        if ($user->isPosUser()) {
            return redirect()->route('pos.index');
        }

        return redirect('/');
    }

    // Log failed login attempts before raising the exception
    protected function sendFailedLoginResponse(Request $request)
    {
        LoginActivityLog::log('failed', $request->input($this->username()));

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    // Capture email before session is invalidated, then log
    public function logout(Request $request)
    {
        $email = $request->user()?->email ?? 'unknown';

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        LoginActivityLog::log('logout', $email);

        return redirect('/');
    }
}
