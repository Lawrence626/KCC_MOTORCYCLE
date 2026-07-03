<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
<<<<<<< HEAD
use Illuminate\Support\Facades\Hash;
=======
use App\Mail\SendOtpMail;
>>>>>>> 55261fdf8c0856ecab9d26ddd532e9ca004a9774
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
<<<<<<< HEAD
    /**
     * Send password reset code to user's email
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        $email = $request->email;
        
        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Delete any existing unused tokens for this email
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('used', false)
            ->delete();
        
        // Save new token
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $code,
=======
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $email = $request->email;

        // Delete any existing OTP for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in database with 15 minute expiration
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'otp' => $otp,
>>>>>>> 55261fdf8c0856ecab9d26ddd532e9ca004a9774
            'expires_at' => Carbon::now()->addMinutes(15),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
<<<<<<< HEAD
        
        // Send email (for now, just log - user will need to configure mail)
        try {
            Mail::raw("Your password reset code is: {$code}\n\nThis code will expire in 15 minutes.", function($message) use ($email) {
                $message->to($email)
                    ->subject('Password Reset Code');
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Reset code sent to your email. Please check your inbox.',
            ]);
        } catch (\Exception $e) {
            // For development, return the code in the response
            return response()->json([
                'success' => true,
                'message' => 'Reset code sent to your email. (Development mode: code is ' . $code . ')',
                'dev_code' => $code,
            ]);
        }
    }
    
    /**
     * Verify the reset code
     */
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
    
    /**
     * Reset password with verified code
     */
=======

        // Send OTP email
        Mail::to($email)->send(new SendOtpMail($otp, $email));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully to your email.',
            'email' => $email
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
            'otp' => 'required|digits:6'
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP. Please try again.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'redirect_url' => route('reset-password', ['email' => $request->email])
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

>>>>>>> 55261fdf8c0856ecab9d26ddd532e9ca004a9774
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
<<<<<<< HEAD
            'code' => 'required|string|size:6',
            'password' => 'required|min:12|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?]/',
            'password_confirmation' => 'required|same:password',
        ], [
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character (@ $ ! % * # ?).',
            'password.min' => 'Password must be at least 12 characters.',
        ]);
        
        // Verify code again
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
        
        // Update user password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }
        
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Mark token as used
        DB::table('password_reset_tokens')
            ->where('id', $token->id)
            ->update(['used' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now login with your new password.',
=======
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        // Verify OTP is still valid
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetToken) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please request a new OTP.'
            ]);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        // Delete the OTP token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now login with your new password.',
            'redirect_url' => route('login')
>>>>>>> 55261fdf8c0856ecab9d26ddd532e9ca004a9774
        ]);
    }
}
