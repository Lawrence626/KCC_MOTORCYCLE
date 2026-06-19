<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginOtpCodeMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginOtpController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
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
        $expiresAt = now()->addMinutes(10);

        Session::put('login.otp', [
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => $otpCode,
            'expires_at' => $expiresAt->timestamp,
            'remember' => $request->boolean('remember'),
        ]);

        Mail::to($user->email)->send(new LoginOtpCodeMail($user, $otpCode));

        return response()->json([
            'message' => 'A 6-digit verification code has been sent to your email address.',
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $otpData = Session::get('login.otp');

        if (! $otpData) {
            return response()->json(['message' => 'No verification request found. Please login again.'], 422);
        }

        if (now()->timestamp > ($otpData['expires_at'] ?? 0)) {
            Session::forget('login.otp');

            return response()->json(['message' => 'The verification code has expired. Please login again.'], 422);
        }

        if ($request->code !== $otpData['code']) {
            return response()->json(['message' => 'The verification code is incorrect. Please try again.'], 422);
        }

        $user = User::find($otpData['user_id']);

        if (! $user) {
            Session::forget('login.otp');

            return response()->json(['message' => 'Unable to verify login. Please try again.'], 422);
        }

        Auth::loginUsingId($user->id, $otpData['remember'] ?? false);
        Session::forget('login.otp');

        return response()->json(['redirectUrl' => route('dashboard')]);
    }
}
