<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCC - Welcome Page</title>
    @vite(['resources/css/app.css', 'resources/css/login.css', 'resources/js/login.js'])
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

                <!-- Right Side - Sign In Form -->
                <div class="flex justify-center lg:justify-start">
                    <div class="sign-in-card w-full max-w-md">
                        <h1 class="sign-in-title">Sign in</h1>

                        <form id="loginForm" class="space-y-6" autocomplete="off">
                            @csrf

                            <div class="form-group">
                                <label for="email">Email</label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="email"
                                        name="email"
                                        placeholder="Enter email address"
                                        class="form-input"
                                        value="{{ old('email') }}"
                                        autocomplete="off"
                                        required
                                    >
                                </div>
                                @error('email')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        placeholder="Enter Password"
                                        class="form-input"
                                        autocomplete="off"
                                        required
                                    >
                                    <button type="button" class="eye-icon-button" onclick="togglePassword(this)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <input type="hidden" name="remember" id="rememberInput" value="0">

                            <div class="form-footer">
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        id="rememberMe"
                                        class="remember-checkbox"
                                        value="1"
                                    >
                                    <span class="ml-2 text-sm text-gray-300">Remember me</span>
                                </label>
                                <a href="{{ url('/forgot-password') }}" class="forgot-password-link">
                                    Forgot Password?
                                </a>
                            </div>

                            <button
                                type="button"
                                id="loginButton"
                                class="login-button"
                            >
                                Log In
                            </button>
                        </form>

                        <div id="otpModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 px-4 py-10 backdrop-blur-sm">
                            <div class="otp-modal-card w-full max-w-md">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h2 class="otp-modal-title">Enter verification code</h2>
                                        <p class="otp-modal-message">We sent a 6-digit code to your email. Enter it here to finish login.</p>
                                    </div>
                                    <button type="button" onclick="hideOtpModal()" class="otp-close-button">✕</button>
                                </div>

                                <div class="mt-6">
                                    <label for="otpCode" class="block text-sm font-medium text-slate-700">Verification code</label>
                                    <input id="otpCode" type="text" maxlength="6" class="otp-input mt-3" placeholder="000000" autocomplete="off" />
                                </div>

                                <div id="otpError" class="mt-3 text-sm text-red-600 hidden"></div>
                                <div id="otpStatus" class="mt-3 text-sm text-emerald-600 hidden"></div>

                                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                                    <button id="otpVerifyButton" type="button" class="otp-button-primary">Verify Code</button>
                                    <button id="otpResendButton" type="button" class="otp-button-secondary">Resend Code</button>
                                    <button id="otpCancelButton" type="button" class="otp-button-tertiary">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const rememberCheckbox = document.getElementById('rememberMe');
        const rememberInput = document.getElementById('rememberInput');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginButton = document.getElementById('loginButton');
        const otpModal = document.getElementById('otpModal');
        const otpCodeInput = document.getElementById('otpCode');
        const otpVerifyButton = document.getElementById('otpVerifyButton');
        const otpResendButton = document.getElementById('otpResendButton');
        const otpCancelButton = document.getElementById('otpCancelButton');
        const otpError = document.getElementById('otpError');
        const otpStatus = document.getElementById('otpStatus');
        const loginForm = document.getElementById('loginForm');

        rememberCheckbox.addEventListener('change', function() {
            rememberInput.value = this.checked ? '1' : '0';
        });

        window.togglePassword = function(button) {
            const input = button.previousElementSibling;
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
        };

        function showOtpModal() {
            otpModal.classList.remove('hidden');
            otpError.classList.add('hidden');
            otpCodeInput.value = '';
        }

        function hideOtpModal() {
            otpModal.classList.add('hidden');
            otpError.classList.add('hidden');
            otpCodeInput.value = '';
        }

        async function sendOtpRequest() {
            otpError.classList.add('hidden');
            const payload = {
                email: emailInput.value.trim(),
                password: passwordInput.value,
                remember: rememberInput.value === '1' ? 1 : 0,
            };

            try {
                const response = await fetch('{{ url('/login/otp/send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (! response.ok) {
                    otpError.textContent = data.message || 'Unable to send verification code.';
                    otpError.classList.remove('hidden');
                    return;
                }

                showOtpModal();
            } catch (error) {
                otpError.textContent = 'Unable to send verification code. Please try again.';
                otpError.classList.remove('hidden');
            }
        }

        async function verifyOtpCode() {
            otpError.classList.add('hidden');

            try {
                const response = await fetch('{{ url('/login/otp/verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: otpCodeInput.value.trim() }),
                });

                const data = await response.json();

                if (! response.ok) {
                    otpError.textContent = data.message || 'The verification code is incorrect.';
                    otpError.classList.remove('hidden');
                    return;
                }

                window.location.href = data.redirectUrl || '{{ route('dashboard') }}';
            } catch (error) {
                otpError.textContent = 'Unable to verify code. Please try again.';
                otpError.classList.remove('hidden');
            }
        }

        async function resendOtpCode() {
            otpError.classList.add('hidden');
            otpStatus.classList.add('hidden');
            otpResendButton.disabled = true;

            try {
                const response = await fetch('{{ url('/login/otp/send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: emailInput.value.trim(),
                        password: passwordInput.value,
                        remember: rememberInput.value === '1' ? 1 : 0,
                    }),
                });

                const data = await response.json();

                if (! response.ok) {
                    otpError.textContent = data.message || 'Unable to resend verification code.';
                    otpError.classList.remove('hidden');
                    otpResendButton.disabled = false;
                    return;
                }

                // Show success message
                otpStatus.textContent = '✓ Verification code sent successfully!';
                otpStatus.classList.remove('hidden');
                otpCodeInput.value = '';
                otpCodeInput.focus();

                // Re-enable button after 3 seconds
                setTimeout(() => {
                    otpResendButton.disabled = false;
                }, 3000);
            } catch (error) {
                otpError.textContent = 'Unable to resend verification code. Please try again.';
                otpError.classList.remove('hidden');
                otpResendButton.disabled = false;
            }
        }

        loginButton.addEventListener('click', function() {
            sendOtpRequest();
        });

        otpVerifyButton.addEventListener('click', function() {
            verifyOtpCode();
        });

        otpResendButton.addEventListener('click', function() {
            resendOtpCode();
        });

        otpCancelButton.addEventListener('click', function() {
            hideOtpModal();
        });
    </script>
</body>
</html>
