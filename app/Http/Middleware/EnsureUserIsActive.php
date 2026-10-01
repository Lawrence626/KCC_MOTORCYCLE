<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = $request->user();

            if ($user && ! ($user->is_active ?? true)) {
                Auth::logout();

                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                $message = 'Your account has been deactivated. Please contact the administrator.';

                if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                    return response()->json([
                        'deactivated' => true,
                        'message'     => $message,
                        'redirectUrl' => url('/login'),
                    ], 401);
                }

                return redirect()->route('login')->with('error', $message);
            }
        } catch (\Throwable $e) {
            // Failsafe: if an issue arises checking user active status, do not crash the app
            \Illuminate\Support\Facades\Log::warning('EnsureUserIsActive middleware error: ' . $e->getMessage());
        }

        return $next($request);
    }
}
