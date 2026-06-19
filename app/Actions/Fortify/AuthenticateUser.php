<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticateUser
{
    /**
     * Authenticate the user and check if account is active.
     */
    public function __invoke($request)
    {
        $user = User::where(config('fortify.username'), $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                config('fortify.username') => [trans('auth.failed')],
            ]);
        }

        // Check if user account is active
        if (! $user->is_active) {
            throw ValidationException::withMessages([
                config('fortify.username') => ['Your account has been archived and cannot access the system.'],
            ]);
        }

        return $user;
    }
}
