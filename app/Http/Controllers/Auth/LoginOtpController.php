<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginOtpCodeMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginOtpController extends Controller
{
    /**
     * Build the cache key for a given email address.
     * Using a hash to avoid exposing the raw email in cache keys.
     */
    private function cacheKey(string $email): string
    {
        return 'login_otp_' . hash('sha256', strtolower(trim($email)));
    }

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password.',
            ], 422);
        }

        if (! ($user->is_active ?? true)) {
            return response()->json([
                'message' => 'Your account has been archived and cannot access the system.',
            ], 422);
        }

        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in the cache (not the session).
        // Session storage was unreliable here because the database session driver
        // doesn't always flush before the browser fires the subsequent verify request.
        // Cache::put() writes to the cache store (also database-backed) synchronously
        // and is not tied to the browser's session cookie, making it much more reliable.
        Cache::put($this->cacheKey($user->email), [
            'user_id'  => $user->id,
            'code'     => $otpCode,
            'remember' => $request->boolean('remember'),
        ], now()->addMinutes(10));

        Mail::to($user->email)->send(new LoginOtpCodeMail($user, $otpCode));

        return response()->json([
            'message' => 'A 6-digit verification code has been sent to your email address.',
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required', 'digits:6'],
        ]);

        $cacheKey = $this->cacheKey($request->email);
        $otpData  = Cache::get($cacheKey);

        if (! $otpData) {
            return response()->json([
                'message' => 'No verification request found. Please login again.',
            ], 422);
        }

        if ($request->code !== $otpData['code']) {
            return response()->json([
                'message' => 'The verification code is incorrect. Please try again.',
            ], 422);
        }

        $user = User::find($otpData['user_id']);

        if (! $user) {
            Cache::forget($cacheKey);
            return response()->json([
                'message' => 'Unable to verify login. Please try again.',
            ], 422);
        }

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::loginUsingId($user->id, $otpData['remember'] ?? false);

        // Remove the OTP from cache once used — one-time use only
        Cache::forget($cacheKey);

        return response()->json(['redirectUrl' => route('dashboard')]);
    }
}
