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
                                        type="email"
                                        id="email"
                                        name="email"
                                        placeholder="Enter Email"
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
                                    <button type="button" class="eye-icon-button" data-visible="false" onclick="togglePassword(this)" aria-label="Show password">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.488m.518-.59A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.074 5.123M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
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

                        <div id="otpModal" class="otp-modal-overlay hidden">
                            <div class="otp-modal-content">
                                <div class="otp-modal-header">
                                    <div class="otp-modal-text">
                                        <h2 class="otp-modal-title">Enter verification code</h2>
                                        <p class="otp-modal-description">We sent a 6-digit code to your email. Enter it here to finish login.</p>
                                    </div>
                                    <button type="button" class="otp-close-btn" onclick="hideOtpModal()">×</button>
                                </div>

                                <div class="otp-form-group">
                                    <label class="otp-label">Verification code</label>
                                    <div class="otp-inputs">
                                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" autocomplete="off" />
                                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" autocomplete="off" />
                                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" autocomplete="off" />
                                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" autocomplete="off" />
                                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" autocomplete="off" />
                                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" autocomplete="off" />
                                    </div>
                                </div>

                                <div id="otpError" class="otp-error hidden"></div>
                                <div id="otpStatus" class="otp-status hidden"></div>

                                <div class="otp-button-group">
                                    <button id="otpVerifyButton" type="button" class="otp-button-verify">Verify Code</button>
                                    <button id="otpResendButton" type="button" class="otp-button-resend">Resend Code</button>
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
        const otpDigits = document.querySelectorAll('.otp-digit');
        const otpVerifyButton = document.getElementById('otpVerifyButton');
        const otpResendButton = document.getElementById('otpResendButton');
        const otpError = document.getElementById('otpError');
        const otpStatus = document.getElementById('otpStatus');
        const loginForm = document.getElementById('loginForm');

        // Setup OTP digit inputs
        otpDigits.forEach((digit, index) => {
            digit.addEventListener('input', (e) => {
                // Only allow numeric input
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                
                // Move to next input if filled
                if (e.target.value.length === 1 && index < otpDigits.length - 1) {
                    otpDigits[index + 1].focus();
                }
            });

            digit.addEventListener('keydown', (e) => {
                // Handle backspace
                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                    otpDigits[index - 1].focus();
                }
                
                // Allow arrow keys and other navigation
                if (['ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
                    if (e.key === 'ArrowLeft' && index > 0) {
                        otpDigits[index - 1].focus();
                        e.preventDefault();
                    } else if (e.key === 'ArrowRight' && index < otpDigits.length - 1) {
                        otpDigits[index + 1].focus();
                        e.preventDefault();
                    }
                }
            });

            digit.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                const digits = pastedData.replace(/[^0-9]/g, '').split('');
                
                digits.forEach((digit, i) => {
                    if (index + i < otpDigits.length) {
                        otpDigits[index + i].value = digit;
                    }
                });
                
                if (digits.length > 0) {
                    const nextIndex = Math.min(index + digits.length - 1, otpDigits.length - 1);
                    otpDigits[nextIndex].focus();
                }
            });
        });

        rememberCheckbox.addEventListener('change', function() {
            rememberInput.value = this.checked ? '1' : '0';
        });

        window.togglePassword = function(button) {
            const input = button.previousElementSibling;
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            const svg = button.querySelector('svg');

            if (visible) {
                button.setAttribute('aria-label', 'Hide password');
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            } else {
                button.setAttribute('aria-label', 'Show password');
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.488m.518-.59A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.074 5.123M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                `;
            }
        };

        function showOtpModal() {
            otpModal.classList.remove('hidden');
            otpError.classList.add('hidden');
            otpStatus.classList.add('hidden');
            otpDigits.forEach(digit => digit.value = '');
            otpDigits[0].focus();
        }

        function hideOtpModal() {
            otpModal.classList.add('hidden');
            otpError.classList.add('hidden');
            otpStatus.classList.add('hidden');
            otpDigits.forEach(digit => digit.value = '');
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

            // Collect all 6 digits
            const code = Array.from(otpDigits).map(digit => digit.value).join('');

            // Validate that all digits are filled
            if (code.length !== 6) {
                otpError.textContent = 'Please enter all 6 digits of the verification code.';
                otpError.classList.remove('hidden');
                return;
            }

            try {
                const response = await fetch('{{ url('/login/otp/verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: code }),
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
                otpDigits.forEach(digit => digit.value = '');
                otpDigits[0].focus();

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

        // Enter key support for login form
        emailInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                passwordInput.focus();
            }
        });

        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendOtpRequest();
            }
        });

        // Enter key support for OTP verification on each digit
        otpDigits.forEach((digit) => {
            digit.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    verifyOtpCode();
                }
            });
        });

        otpVerifyButton.addEventListener('click', function() {
            verifyOtpCode();
        });

        otpResendButton.addEventListener('click', function() {
            resendOtpCode();
        });
    </script>
</body>
</html>
