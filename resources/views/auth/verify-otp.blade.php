<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCC - Verify OTP</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/Logo.png') }}">
    @vite(['resources/css/app.css', 'resources/css/login.css'])
    <style>
        .verify-otp-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        .verify-otp-card {
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
        .verify-otp-title {
            font-family: 'Poppins', sans-serif;
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 12px;
        }
        .verify-otp-description {
            color: #E5E5E5;
            font-size: 13px;
            text-align: center;
            margin-bottom: 35px;
            line-height: 1.6;
            opacity: 0.85;
        }
        .verify-otp-button {
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
        .verify-otp-button:hover {
            background: #005f57;
        }
        .verify-otp-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .secondary-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 20px;
        }
        .secondary-button {
            height: 40px;
            border: none;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-weight: 500;
            font-size: 13px;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .secondary-button:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .secondary-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="welcome-background" style="background-image: url('{{ asset('images/background.png') }}');">
        <div class="welcome-overlay"></div>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-12">
        <div class="verify-otp-container">
            <div class="verify-otp-card">
                <div class="step-indicator">
                    <div class="step">
                        <div class="step-circle completed">✓</div>
                        <span class="step-label completed">Email</span>
                    </div>
                    <div class="step">
                        <div class="step-circle active">2</div>
                        <span class="step-label active">OTP</span>
                    </div>
                    <div class="step">
                        <div class="step-circle inactive">3</div>
                        <span class="step-label">Reset</span>
                    </div>
                </div>
                <h1 class="verify-otp-title">Verify OTP</h1>
                <p class="verify-otp-description">Enter the 6-digit OTP sent to <span style="color: #4A9CA5;">{{ $email }}</span></p>

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
                        class="verify-otp-button"
                    >
                        Verify OTP
                    </button>

                    <div class="secondary-buttons">
                        <button
                            type="button"
                            id="resendOtpButton"
                            class="secondary-button"
                        >
                            Resend OTP
                        </button>
                        <button
                            type="button"
                            id="backButton"
                            class="secondary-button"
                        >
                            Back
                        </button>
                    </div>
                </form>
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
