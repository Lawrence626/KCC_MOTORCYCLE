<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCC - Reset Password</title>
    @vite(['resources/css/app.css', 'resources/css/login.css'])
    <style>
        .reset-password-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        .reset-password-card {
            width: 100%;
            padding: 50px 40px 60px;
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
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin-bottom: 25px;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 60px;
            right: 60px;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 0;
        }
        .step {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .step-circle.active {
            background: #4A9CA5;
            color: white;
            box-shadow: 0 0 0 3px rgba(74, 156, 165, 0.3);
        }
        .step-circle.inactive {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        .step-circle.completed {
            background: #4eca7c;
            color: white;
        }
        .step-label {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .step-label.active {
            color: #4A9CA5;
            font-weight: 600;
        }
        .step-label.completed {
            color: #4eca7c;
            font-weight: 600;
        }
        .reset-password-title {
            font-family: 'Poppins', sans-serif;
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 12px;
        }
        .reset-password-description {
            color: #E5E5E5;
            font-size: 13px;
            text-align: center;
            margin-bottom: 35px;
            line-height: 1.6;
            opacity: 0.85;
        }
        .reset-password-button {
            width: 100%;
            height: 45px;
            border: none;
            border-radius: 50px;
            background: #4A9CA5;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        .reset-password-button:hover {
            background: #005f57;
        }
        .reset-password-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .back-to-login {
            text-align: center;
            margin-top: 25px;
        }
        .back-to-login a {
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            transition: opacity 0.2s;
        }
        .back-to-login a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="welcome-background" style="background-image: url('{{ asset('images/background.png') }}');">
        <div class="welcome-overlay"></div>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-12">
        <div class="reset-password-container">
            <div class="reset-password-card">
                <div class="step-indicator">
                    <div class="step">
                        <div class="step-circle completed">✓</div>
                        <span class="step-label completed">Email</span>
                    </div>
                    <div class="step">
                        <div class="step-circle completed">✓</div>
                        <span class="step-label completed">OTP</span>
                    </div>
                    <div class="step">
                        <div class="step-circle active">3</div>
                        <span class="step-label active">Reset</span>
                    </div>
                </div>
                <h1 class="reset-password-title">Reset Password</h1>
                <p class="reset-password-description">Create a new password for your account.</p>

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
                        class="reset-password-button"
                    >
                        Reset Password
                    </button>

                    <div class="back-to-login">
                        <a href="{{ route('login') }}">
                            Back to Login
                        </a>
                    </div>
                </form>
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
