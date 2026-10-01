<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Mail\SendOtpMail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showForgotPassword()
    {
        return view('auth.forgot-password-page');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && ! ($user->is_active ?? true)) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been archived and cannot access the system.',
            ], 403);
        }

        $email = $request->email;
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $code,
            'otp' => $code,
            'expires_at' => Carbon::now()->addMinutes(15),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $isSent = \App\Services\ResendEmailService::sendResetOtp($email, $code);

        return response()->json([
            'success' => true,
            'is_sent' => $isSent,
            'message' => 'Reset code sent to your email. Please check your inbox.',
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $token = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where(function ($query) use ($request) {
                $query->where('token', $request->code)->orWhere('otp', $request->code);
            })
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired code.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code verified successfully.',
        ]);
    }

    public function showResetPassword(Request $request)
    {
        $email = $request->query('email');
        if (!$email) {
            return redirect()->route('forgot-password');
        }

        return view('auth.reset-password', compact('email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'nullable|string',
            'password' => [
                'required',
                'string',
                'min:12',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^()\-]).+$/',
                'confirmed',
            ],
        ], [
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'password.min' => 'Password must be at least 12 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $tokenQuery = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('expires_at', '>', Carbon::now());

        if ($request->filled('code')) {
            $tokenQuery->where(function ($query) use ($request) {
                $query->where('token', $request->code)->orWhere('otp', $request->code);
            });
        }

        $token = $tokenQuery->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Your password reset session has expired or is invalid. Please request a new OTP.',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->password = $request->password;
        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now login with your new password.',
            'redirect_url' => route('login'),
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && ! ($user->is_active ?? true)) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been archived and cannot access the system.',
            ], 403);
        }

        $email = $request->email;
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $otp,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(15),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $isSent = \App\Services\ResendEmailService::sendResetOtp($email, $otp);

        return response()->json([
            'success' => true,
            'is_sent' => $isSent,
            'message' => 'Reset code sent to your email. Please check your inbox.',
        ]);
    }

    public function showVerifyOtp(Request $request)
    {
        $email = $request->query('email');
        if (!$email) {
            return redirect()->route('forgot-password');
        }

        return view('auth.verify-otp', compact('email'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where(function ($query) use ($request) {
                $query->where('otp', $request->otp)->orWhere('token', $request->otp);
            })
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP. Please try again.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'redirect_url' => route('reset-password', ['email' => $request->email]),
        ]);
    }
}
