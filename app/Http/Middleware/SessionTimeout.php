<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    private int $timeoutSeconds;

    public function __construct()
    {
        $this->timeoutSeconds = (int) config('session.inactivity_timeout', 1800);
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $lastActivity = session('_last_activity_at');
        $now          = time();

        if ($lastActivity !== null) {
            $inactiveFor = $now - $lastActivity;

            if ($inactiveFor > $this->timeoutSeconds) {
                AuditLog::record(
                    action:   AuditLog::ACTION_SESSION_EXPIRED,
                    userId:   Auth::id(),
                    metadata: ['inactive_seconds' => $inactiveFor],
                );

                Auth::logout();
                $request->session()->flush();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->with('warning', 'Sua sessão expirou por inatividade. Faça login novamente.');
            }
        }

        session(['_last_activity_at' => $now]);

        return $next($request);
    }
}
