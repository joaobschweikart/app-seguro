<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, remember: false)) {

            $request->session()->regenerate();

            AuditLog::record(
                action:   AuditLog::ACTION_LOGIN_SUCCESS,
                userId:   Auth::id(),
                metadata: ['email' => $credentials['email']],
            );

            session(['_last_activity_at' => time()]);

            return redirect()->intended(route('dashboard'));
        }

        AuditLog::record(
            action:   AuditLog::ACTION_LOGIN_FAILED,
            userId:   null,
            metadata: ['email_attempted' => $credentials['email']],
        );

        $request->incrementThrottleAttempts();

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => __('As credenciais informadas não conferem.'),
            ]);
    }

    public function logout(\Illuminate\Http\Request $request): RedirectResponse
    {
        AuditLog::record(
            action: AuditLog::ACTION_LOGOUT,
            userId: Auth::id(),
        );

        Auth::logout();

        $request->session()->invalidate(); 
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Logout realizado com sucesso.');
    }
}
