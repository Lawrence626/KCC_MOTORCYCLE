<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
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

        $email = $request->email;
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $code,
            'expires_at' => Carbon::now()->addMinutes(15),
            'used' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        try {
            Mail::raw("Your password reset code is: {$code}\n\nThis code will expire in 15 minutes.", function ($message) use ($email) {
                $message->to($email)->subject('Password Reset Code');
            });

            return response()->json([
                'success' => true,
                'message' => 'Reset code sent to your email. Please check your inbox.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'Reset code generated successfully. (Development mode: code is ' . $code . ')',
                'dev_code' => $code,
            ]);
        }
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $token = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->where('used', false)
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
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|min:12|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?]/',
            'password_confirmation' => 'required|same:password',
        ], [
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character (@ $ ! % * # ?).',
            'password.min' => 'Password must be at least 12 characters.',
        ]);

        $token = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where(function ($query) use ($request) {
                $query->where('token', $request->code)->orWhere('otp', $request->code);
            })
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired code.',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')
            ->where('id', $token->id)
            ->update(['used' => true]);

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
        ]);

        $email = $request->email;
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(15),
            'used' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        try {
            Mail::raw("Your password reset code is: {$otp}\n\nThis code will expire in 15 minutes.", function ($message) use ($email) {
                $message->to($email)->subject('Password Reset OTP');
            });

            return response()->json([
                'success' => true,
                'message' => 'Reset code sent to your email. Please check your inbox.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'Reset code generated successfully. (Development mode: code is ' . $otp . ')',
                'dev_code' => $otp,
            ]);
        }
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
            ->where('otp', $request->otp)
            ->where('used', false)
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
