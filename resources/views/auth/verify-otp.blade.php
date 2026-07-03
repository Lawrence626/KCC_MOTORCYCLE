<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCC - Verify OTP</title>
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

                <!-- Right Side - OTP Verification Form -->
                <div class="flex justify-center lg:justify-start">
                    <div class="sign-in-card w-full max-w-md">
                        <h1 class="sign-in-title">Verify OTP</h1>
                        <p class="text-gray-300 text-center mb-6">Enter the 6-digit OTP sent to <span class="text-teal-400">{{ $email }}</span></p>

                        <form id="verifyOtpForm" class="space-y-6" autocomplete="off">
                            @csrf
                            <input type="hidden" id="email" name="email" value="{{ $email }}">

                            <div class="form-group">
                                <label for="otp">OTP Code</label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="otp"
                                        name="otp"
                                        placeholder="Enter 6-digit OTP"
                                        class="form-input"
                                        maxlength="6"
                                        autocomplete="off"
                                        required
                                    >
                                </div>
                                <div id="otpError" class="error-message hidden"></div>
                                <div id="otpStatus" class="mt-3 text-sm text-emerald-600 hidden"></div>
                            </div>

                            <button
                                type="button"
                                id="verifyOtpButton"
                                class="login-button"
                            >
                                Verify OTP
                            </button>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <button
                                    type="button"
                                    id="resendOtpButton"
                                    class="otp-button-secondary"
                                >
                                    Resend OTP
                                </button>
                                <button
                                    type="button"
                                    id="backButton"
                                    class="otp-button-tertiary"
                                >
                                    Back
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const emailInput = document.getElementById('email');
        const otpInput = document.getElementById('otp');
        const verifyOtpButton = document.getElementById('verifyOtpButton');
        const resendOtpButton = document.getElementById('resendOtpButton');
        const backButton = document.getElementById('backButton');
        const otpError = document.getElementById('otpError');
        const otpStatus = document.getElementById('otpStatus');

        async function verifyOtp() {
            otpError.classList.add('hidden');
            otpStatus.classList.add('hidden');
            verifyOtpButton.disabled = true;
            verifyOtpButton.textContent = 'Verifying...';

            const email = emailInput.value;
            const otp = otpInput.value.trim();

            try {
                const response = await fetch('{{ route('verify-otp.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email, otp: otp }),
                });

                const data = await response.json();

                if (!response.ok) {
                    otpError.textContent = data.message || 'Invalid OTP. Please try again.';
                    otpError.classList.remove('hidden');
                    verifyOtpButton.disabled = false;
                    verifyOtpButton.textContent = 'Verify OTP';
                    return;
                }

                // Redirect to reset password page
                window.location.href = data.redirect_url;
            } catch (error) {
                otpError.textContent = 'Unable to verify OTP. Please try again.';
                otpError.classList.remove('hidden');
                verifyOtpButton.disabled = false;
                verifyOtpButton.textContent = 'Verify OTP';
            }
        }

        async function resendOtp() {
            otpError.classList.add('hidden');
            otpStatus.classList.add('hidden');
            resendOtpButton.disabled = true;
            resendOtpButton.textContent = 'Sending...';

            const email = emailInput.value;

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
                    otpError.textContent = data.message || 'Unable to resend OTP. Please try again.';
                    otpError.classList.remove('hidden');
                    resendOtpButton.disabled = false;
                    resendOtpButton.textContent = 'Resend OTP';
                    return;
                }

                // Show success message
                otpStatus.textContent = '✓ OTP sent successfully!';
                otpStatus.classList.remove('hidden');
                otpInput.value = '';
                otpInput.focus();

                // Re-enable button after 3 seconds
                setTimeout(() => {
                    resendOtpButton.disabled = false;
                    resendOtpButton.textContent = 'Resend OTP';
                }, 3000);
            } catch (error) {
                otpError.textContent = 'Unable to resend OTP. Please try again.';
                otpError.classList.remove('hidden');
                resendOtpButton.disabled = false;
                resendOtpButton.textContent = 'Resend OTP';
            }
        }

        verifyOtpButton.addEventListener('click', function() {
            verifyOtp();
        });

        resendOtpButton.addEventListener('click', function() {
            resendOtp();
        });

        backButton.addEventListener('click', function() {
            window.location.href = '{{ route('forgot-password') }}';
        });

        // Enter key support
        otpInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                verifyOtp();
            }
        });

        // Auto-format OTP input (only numbers)
        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    </script>
</body>
</html>
