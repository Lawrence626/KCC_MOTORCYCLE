<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action. Only administrators can archive users.');
        }

        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot archive your own administrator account.');
        }

        $user->is_active = ! ($user->is_active ?? true);

        if (! $user->is_active) {
            $user->remember_token = null;
        }

        $user->save();

        // If the user was archived/deactivated, instantly delete all active sessions from the DB
        if (! $user->is_active) {
            try {
                \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();
            } catch (\Throwable $e) {
                // Ignore if sessions table not present or file driver
            }
        }

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

    public function showProfile()
    {
        // Force a fresh DB query so the view always sees the latest avatar/fields
        auth()->setUser(auth()->user()->fresh());
        return view('profile.show');
    }

    /**
     * Update the authenticated user's profile (self-service)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,'.$user->id,
            'contact' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'age'     => 'nullable|integer|min:0',
            'gender'  => 'nullable|string|max:50',
        ];

        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                'min:12',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^()\-]).+$/',
                'confirmed',
            ];
        }

        if ($request->hasFile('avatar')) {
            $rules['avatar'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120';
        }

        $data = $request->validate($rules, [
            'password.regex'     => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'password.confirmed' => 'The password confirmation does not match.',
            'avatar.max'         => 'The profile picture must not be larger than 5MB. Please select a smaller file.',
            'avatar.image'       => 'The profile picture must be a valid image file (JPG, PNG, GIF, WEBP).',
            'avatar.mimes'       => 'The profile picture must be a file of type: jpeg, png, jpg, gif, webp.',
        ]);

        $hasValidAvatar = $request->hasFile('avatar') && $request->file('avatar')->isValid();

        // ALWAYS remove avatar from $data immediately — it may contain an UploadedFile
        // object which, if passed to update(), gets cast to the raw Windows temp path.
        unset($data['avatar']);

        // Only update password when explicitly provided
        if (!$request->filled('password')) {
            unset($data['password']);
        }

        // Store the avatar and add the STORAGE path string to $data.
        // Only runs when a valid file was actually uploaded.
        if ($hasValidAvatar) {
            try {
                $file = $request->file('avatar');

                // Determine extension safely
                $extension = strtolower($file->getClientOriginalExtension());
                if ($extension === '') {
                    $mimeMap = [
                        'image/jpeg' => 'jpg',
                        'image/jpg'  => 'jpg',
                        'image/png'  => 'png',
                        'image/gif'  => 'gif',
                        'image/webp' => 'webp',
                    ];
                    $mime      = $file->getMimeType() ?? '';
                    $extension = $mimeMap[$mime] ?? ($file->extension() ?: 'jpg');
                }

                $filename = 'avatar_' . $user->id . '_' . time() . '.' . $extension;

                // Ensure directory exists
                Storage::disk('public')->makeDirectory('avatars');

                // Read file content safely across platforms (handling Windows realpath returning false)
                $tempPath = $file->getRealPath() ?: $file->getPathname();
                $contents = false;

                if (!empty($tempPath) && file_exists($tempPath)) {
                    $contents = @file_get_contents($tempPath);
                }

                if ($contents === false || $contents === null) {
                    $contents = $file->getContent();
                }

                if ($contents !== false && $contents !== null) {
                    $stored = Storage::disk('public')->put('avatars/' . $filename, $contents);
                    if ($stored) {
                        // Delete previous avatar file if exists
                        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                            Storage::disk('public')->delete($user->avatar);
                        }
                        $data['avatar'] = 'avatars/' . $filename;
                    }
                }
            } catch (\Throwable $e) {
                // Storage failed — leave existing photo intact
                \Illuminate\Support\Facades\Log::error('Avatar upload failed: ' . $e->getMessage());
            }
        }

        $user->update($data);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        try {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();
            return redirect()->back()->with('success', 'User permanently deleted successfully.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('User deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
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
