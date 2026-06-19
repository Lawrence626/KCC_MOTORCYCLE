<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('is_active', true);

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();
        return view('user_management.user_manage', compact('users'));
    }

    public function create()
    {
        return view('user_management.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|max:50',
            'contact' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:Male,Female,Other',
            'password' => [
                'required',
                'string',
                'min:12',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^()\-]).+$/',
                'confirmed',
            ],
        ], [
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character (e.g., @ $ ! % * # ? & ^ ( ) -).',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $data['is_active'] = true;

        // Model has a cast for password => 'hashed' so provide plain password
        User::create($data);

        return redirect()->route('user.management')->with('success', 'User added successfully.');
    }

    public function updateStatus(Request $request, User $user)
    {
        $user->is_active = ! ($user->is_active ?? true);
        $user->save();

        $message = $user->is_active ? 'User restored successfully.' : 'User archived successfully.';

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'is_active' => $user->is_active]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|string',
        ]);

        $user->update($data);
        $label = str_replace('_', ' ', $user->role);
        $label = ucwords($label);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return redirect()->back()->with('success', "Role updated to {$label}.");
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|string|max:50',
            'contact' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:Male,Female,Other',
        ];

        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                'min:12',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^()\-]).+$/',
                'confirmed',
            ];
        }

        $data = $request->validate($rules, [
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character (e.g., @ $ ! % * # ? & ^ ( ) -).',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('user.management')->with('success', 'User updated successfully.');
    }

    /**
     * Update the authenticated user's profile (self-service)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'contact' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:Male,Female,Other',
            'avatar' => 'nullable|image|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                'min:12',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^()\-]).+$/',
                'confirmed'
            ];
        }

        $data = $request->validate($rules, [
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        // Handle avatar upload if the file input exists and the users table has an avatar column
        if ($request->hasFile('avatar')) {
            try {
                $avatar = $request->file('avatar');
                $path = $avatar->store('avatars', 'public');
                if (\Schema::hasColumn('users', 'avatar')) {
                    $data['avatar'] = $path;
                }
            } catch (\Throwable $e) {
                // ignore avatar save errors
            }
        }

        // Remove empty password to avoid nullifying
        if (empty($data['password'])) unset($data['password']);

        $user->update($data);

        return redirect()->route('settings.general')->with('success', 'Profile updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted.');
    }

    public function archived(Request $request)
    {
        $query = User::where('is_active', false);

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();
        $showArchived = true;
        return view('user_management.user_manage', compact('users','showArchived'));
    }
}
