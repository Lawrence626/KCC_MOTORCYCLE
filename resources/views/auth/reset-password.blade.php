<!DOCTYPE html>
<<<<<<< HEAD
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - KCC Motorcycle</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .reset-password-container {
            position: relative;
            z-index: 2;
            display: flex;
            width: 100%;
            height: 100vh;
        }

        .reset-password-left {
            width: 55%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .reset-password-right {
            width: 45%;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            padding-left: 40px;
        }

        .reset-password-card {
            width: 380px;
            padding: 50px 35px 40px;
            border-radius: 30px;
            background: linear-gradient(
                180deg,
                rgba(5,5,5,.96) 0%,
                rgba(15,15,15,.95) 60%,
                rgba(150,150,150,.45) 100%
            );
            backdrop-filter: blur(18px);
            box-shadow: 0 20px 50px rgba(0,0,0,.65);
        }

        .reset-password-title {
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 15px;
        }

        .reset-password-description {
            color: #ddd;
            font-size: 12px;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-login a {
            color: #4A9CA5;
            text-decoration: none;
            font-size: 12px;
            transition: color 0.3s;
        }

        .back-to-login a:hover {
            color: #fff;
            text-decoration: underline;
        }

        .submit-button {
            width: 160px;
            height: 34px;
            display: block;
            margin: 25px auto 0;
            border: none;
            border-radius: 50px;
            background: #4A9CA5;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: .3s;
            font-size: 12px;
        }

        .submit-button:hover {
            background: #005f57;
        }

        .submit-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .success-message {
            color: #4eca7c;
            font-size: 12px;
            text-align: center;
            padding: 12px;
            background: rgba(78,202,124,.15);
            border-radius: 8px;
            margin-bottom: 15px;
            display: none;
        }

        .success-message.show {
            display: block;
        }

        .eye-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
        }

        @media(max-width:991px){
            .reset-password-container {
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 30px;
            }

            .reset-password-left,
            .reset-password-right {
                width: 100%;
                justify-content: center;
                padding: 0;
            }

            .reset-password-card {
                width: 100%;
                max-width: 390px;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-background" style="background-image: url('{{ asset('images/background.jpg') }}');"></div>
    <div class="welcome-overlay"></div>

    <div class="reset-password-container">
        <div class="reset-password-left">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="KCC Motorcycle" class="logo-image" />
            </div>
        </div>

        <div class="reset-password-right">
            <div class="reset-password-card">
                <h1 class="reset-password-title">Create New Password</h1>
                <p class="reset-password-description">Enter a new password for your account.</p>

                <div id="successMessage" class="success-message">
                    ✓ Password reset successfully!
                </div>

                <form id="resetPasswordForm">
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">
                    
                    <div class="form-group">
                        <label for="email" class="form-group label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            value="{{ request('email') }}"
                            required
                            readonly
                        />
                        <div id="emailError" class="error-message hidden"></div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-group label">New Password</label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Enter new password"
                                required
                                autocomplete="new-password"
                            />
                            <button type="button" class="eye-icon-button" onclick="togglePassword(this)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <div id="passwordError" class="error-message hidden"></div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-group label">Confirm Password</label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-input"
                                placeholder="Confirm new password"
                                required
                                autocomplete="new-password"
                            />
                            <button type="button" class="eye-icon-button" onclick="togglePassword(this)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <div id="confirmError" class="error-message hidden"></div>
                    </div>

                    <button type="submit" class="submit-button" id="submitButton">Reset Password</button>
                </form>

                <div class="back-to-login">
                    <a href="{{ route('login') }}">← Back to Login</a>
=======
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
>>>>>>> 83faba04c87105623853f9cb40fd6c9b6e50a94a
                </div>
            </div>
        </div>
    </div>

    <script>
<<<<<<< HEAD
        function togglePassword(button) {
            const input = button.previousElementSibling;
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            const svg = button.querySelector('svg');

            if (visible) {
                button.setAttribute('aria-label', 'Hide password');
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.488m.518-.59A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.074 5.123M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                `;
            } else {
                button.setAttribute('aria-label', 'Show password');
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        const form = document.getElementById('resetPasswordForm');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const submitButton = document.getElementById('submitButton');
        const successMessage = document.getElementById('successMessage');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Clear previous errors
            document.getElementById('emailError').classList.add('hidden');
            document.getElementById('passwordError').classList.add('hidden');
            document.getElementById('confirmError').classList.add('hidden');
            successMessage.classList.remove('show');
            
            submitButton.disabled = true;
            submitButton.textContent = 'Resetting...';

            try {
                const response = await fetch('{{ route('password.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        token: document.querySelector('input[name="token"]').value,
                        email: document.getElementById('email').value,
                        password: passwordInput.value,
                        password_confirmation: confirmInput.value
                    })
=======
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
>>>>>>> 83faba04c87105623853f9cb40fd6c9b6e50a94a
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
<<<<<<< HEAD
                        if (data.errors.email) {
                            document.getElementById('emailError').textContent = data.errors.email[0];
                            document.getElementById('emailError').classList.remove('hidden');
                        }
                        if (data.errors.password) {
                            document.getElementById('passwordError').textContent = data.errors.password[0];
                            document.getElementById('passwordError').classList.remove('hidden');
                        }
                    } else {
                        document.getElementById('passwordError').textContent = data.message || 'Failed to reset password.';
                        document.getElementById('passwordError').classList.remove('hidden');
                    }
                } else {
                    successMessage.classList.add('show');
                    setTimeout(() => {
                        window.location.href = '{{ route('login') }}';
                    }, 2000);
                }
            } catch (error) {
                document.getElementById('passwordError').textContent = 'An error occurred. Please try again.';
                document.getElementById('passwordError').classList.remove('hidden');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Reset Password';
=======
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
>>>>>>> 83faba04c87105623853f9cb40fd6c9b6e50a94a
            }
        });
    </script>
</body>
</html>
