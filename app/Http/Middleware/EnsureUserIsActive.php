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
        if (Auth::check()) {
            $user = Auth::user();

            // Refresh user model from DB to catch real-time status change
            if (! ($user->fresh()?->is_active ?? true)) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = 'Your account has been deactivated. Please contact the administrator.';

                if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                    return response()->json([
                        'deactivated' => true,
                        'message'     => $message,
                        'redirectUrl' => route('login'),
                    ], 401);
                }

                return redirect()->route('login')->with('error', $message);
            }
        }

        return $next($request);
    }
}
