<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use App\Models\User;

class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return Response::json([
                'success' => true,
                'message' => 'Password reset link sent to your email.'
            ]);
        }

        return Response::json([
            'success' => false,
            'message' => trans($status)
        ], 422);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return Response::json([
                'success' => true,
                'message' => 'Password reset successfully. Redirecting to login...'
            ]);
        }

        return Response::json([
            'success' => false,
            'message' => trans($status)
        ], 422);
    }
}

