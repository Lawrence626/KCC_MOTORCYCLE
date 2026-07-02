<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCC - Reset Password</title>
    @vite(['resources/css/app.css', 'resources/css/login.css'])
</head>
<body>
    <!-- Background Image Container -->
    <div class="welcome-background" style="background-image: url('{{ asset('images/background.png') }}');">
        <!-- Dark Overlay -->
        <div class="welcome-overlay"></div>
    </div>

    <!-- Content Container -->
    <div class="relative z-10 min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-32 items-center">

                <!-- Left Side - Logo/Branding -->
                <div class="logo-container">
                    <img
                        src="{{ asset('images/Logo.png') }}"
                        alt="KCC Logo"
                        class="logo-image"
                    >
                </div>

                <!-- Right Side - Reset Password Form -->
                <div class="flex justify-center lg:justify-start">
                    <div class="sign-in-card w-full max-w-md">
                        <h1 class="sign-in-title">Reset Password</h1>
                        <p class="text-gray-300 text-center mb-6">Create a new password for <span class="text-teal-400">{{ $email }}</span></p>

                        <form id="resetPasswordForm" class="space-y-6" autocomplete="off">
                            @csrf
                            <input type="hidden" id="email" name="email" value="{{ $email }}">

                            <div class="form-group">
                                <label for="password">New Password</label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        placeholder="Enter new password"
                                        class="form-input"
                                        autocomplete="off"
                                        required
                                    >
                                    <button type="button" class="eye-icon-button" onclick="togglePassword('password', this)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div id="passwordError" class="error-message hidden"></div>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirm New Password</label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="Confirm new password"
                                        class="form-input"
                                        autocomplete="off"
                                        required
                                    >
                                    <button type="button" class="eye-icon-button" onclick="togglePassword('password_confirmation', this)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div id="confirmPasswordError" class="error-message hidden"></div>
                            </div>

                            <div id="successMessage" class="mt-3 text-sm text-emerald-600 hidden"></div>

                            <button
                                type="button"
                                id="resetPasswordButton"
                                class="login-button"
                            >
                                Reset Password
                            </button>

                            <div class="text-center mt-4">
                                <a href="{{ route('login') }}" class="forgot-password-link">
                                    Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const resetPasswordButton = document.getElementById('resetPasswordButton');
        const passwordError = document.getElementById('passwordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');
        const successMessage = document.getElementById('successMessage');

        window.togglePassword = function(inputId, button) {
            const input = document.getElementById(inputId);
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
        };

        async function resetPassword() {
            passwordError.classList.add('hidden');
            confirmPasswordError.classList.add('hidden');
            successMessage.classList.add('hidden');
            resetPasswordButton.disabled = true;
            resetPasswordButton.textContent = 'Resetting...';

            const email = emailInput.value;
            const password = passwordInput.value;
            const passwordConfirmation = confirmPasswordInput.value;

            // Client-side validation
            if (password.length < 8) {
                passwordError.textContent = 'Password must be at least 8 characters long.';
                passwordError.classList.remove('hidden');
                resetPasswordButton.disabled = false;
                resetPasswordButton.textContent = 'Reset Password';
                return;
            }

            if (password !== passwordConfirmation) {
                confirmPasswordError.textContent = 'Passwords do not match.';
                confirmPasswordError.classList.remove('hidden');
                resetPasswordButton.disabled = false;
                resetPasswordButton.textContent = 'Reset Password';
                return;
            }

            try {
                const response = await fetch('{{ route('reset-password.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        email: email, 
                        password: password, 
                        password_confirmation: passwordConfirmation 
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        if (data.errors.password) {
                            passwordError.textContent = data.errors.password[0];
                            passwordError.classList.remove('hidden');
                        }
                        if (data.errors.password_confirmation) {
                            confirmPasswordError.textContent = data.errors.password_confirmation[0];
                            confirmPasswordError.classList.remove('hidden');
                        }
                    } else {
                        passwordError.textContent = data.message || 'Unable to reset password. Please try again.';
                        passwordError.classList.remove('hidden');
                    }
                    resetPasswordButton.disabled = false;
                    resetPasswordButton.textContent = 'Reset Password';
                    return;
                }

                // Show success message
                successMessage.textContent = '✓ ' + data.message;
                successMessage.classList.remove('hidden');
                resetPasswordButton.textContent = 'Success!';

                // Redirect to login after 2 seconds
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 2000);
            } catch (error) {
                passwordError.textContent = 'Unable to reset password. Please try again.';
                passwordError.classList.remove('hidden');
                resetPasswordButton.disabled = false;
                resetPasswordButton.textContent = 'Reset Password';
            }
        }

        resetPasswordButton.addEventListener('click', function() {
            resetPassword();
        });

        // Enter key support
        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                confirmPasswordInput.focus();
            }
        });

        confirmPasswordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                resetPassword();
            }
        });
    </script>
</body>
</html>
