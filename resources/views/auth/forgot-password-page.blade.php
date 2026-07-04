<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCC - Forgot Password</title>
    @vite(['resources/css/app.css', 'resources/css/login.css'])
</head>
<body>
    <div class="welcome-background" style="background-image: url('{{ asset('images/background.png') }}');">
        <div class="welcome-overlay"></div>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_1fr] gap-8 lg:gap-24 items-center">

                <div class="flex justify-center lg:justify-start">
                    <div class="logo-container">
                        <img
                            src="{{ asset('images/Logo.png') }}"
                            alt="KCC Logo"
                            class="logo-image"
                        >
                    </div>
                </div>

                <div class="flex justify-center lg:justify-end">
                    <div class="sign-in-card w-full max-w-md">
                        <h1 class="sign-in-title">Forgot Password</h1>
                        <p class="text-gray-300 text-center mb-6">Enter your email address and we&rsquo;ll send you an OTP to reset your password.</p>

                        <form id="forgotPasswordForm" class="space-y-6" autocomplete="off">
                            @csrf

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="relative">
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        placeholder="Enter your email address"
                                        class="form-input"
                                        autocomplete="off"
                                        required
                                    >
                                </div>
                                <div id="emailError" class="error-message hidden"></div>
                            </div>

                            <button
                                type="button"
                                id="sendOtpButton"
                                class="login-button"
                            >
                                Send OTP
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
        const sendOtpButton = document.getElementById('sendOtpButton');
        const emailError = document.getElementById('emailError');

        async function sendOtp() {
            emailError.classList.add('hidden');
            sendOtpButton.disabled = true;
            sendOtpButton.textContent = 'Sending...';

            const email = emailInput.value.trim();

            try {
                const response = await fetch('{{ route('forgot-password.otp.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email }),
                });

                const data = await response.json();

                if (!response.ok) {
                    emailError.textContent = data.message || 'Unable to send OTP. Please try again.';
                    emailError.classList.remove('hidden');
                    sendOtpButton.disabled = false;
                    sendOtpButton.textContent = 'Send OTP';
                    return;
                }

                window.location.href = '{{ route('verify-otp') }}?email=' + encodeURIComponent(email);
            } catch (error) {
                emailError.textContent = 'Unable to send OTP. Please try again.';
                emailError.classList.remove('hidden');
                sendOtpButton.disabled = false;
                sendOtpButton.textContent = 'Send OTP';
            }
        }

        sendOtpButton.addEventListener('click', sendOtp);

        emailInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendOtp();
            }
        });
    </script>
</body>
</html>
