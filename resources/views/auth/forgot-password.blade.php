<!DOCTYPE html>
<<<<<<< HEAD
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - KCC Motorcycle</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .forgot-password-container {
            position: relative;
            z-index: 2;
            display: flex;
            width: 100%;
            height: 100vh;
        }

        .forgot-password-left {
            width: 55%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .forgot-password-right {
            width: 45%;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            padding-left: 40px;
        }

        .forgot-password-card {
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

        .forgot-password-title {
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 15px;
        }

        .forgot-password-description {
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

        @media(max-width:991px){
            .forgot-password-container {
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 30px;
            }

            .forgot-password-left,
            .forgot-password-right {
                width: 100%;
                justify-content: center;
                padding: 0;
            }

            .forgot-password-card {
                width: 100%;
                max-width: 390px;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-background" style="background-image: url('{{ asset('images/background.jpg') }}');"></div>
    <div class="welcome-overlay"></div>

    <div class="forgot-password-container">
        <div class="forgot-password-left">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="KCC Motorcycle" class="logo-image" />
            </div>
        </div>

        <div class="forgot-password-right">
            <div class="forgot-password-card">
                <h1 class="forgot-password-title">Reset Password</h1>
                <p class="forgot-password-description">Enter your email address and we'll send you a link to reset your password.</p>

                <div id="successMessage" class="success-message">
                    ✓ Password reset link sent to your email!
                </div>

                <form id="forgotPasswordForm">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-group label">Email</label>
                        <div class="relative">
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                placeholder="Enter your email"
                                required
                                autocomplete="email"
                            />
                        </div>
                        <div id="emailError" class="error-message hidden"></div>
                    </div>

                    <button type="submit" class="submit-button" id="submitButton">Send Link</button>
                </form>

                <div class="back-to-login">
                    <a href="{{ route('login') }}">← Back to Login</a>
=======
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCC - Forgot Password</title>
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

                <!-- Right Side - Forgot Password Form -->
                <div class="flex justify-center lg:justify-start">
                    <div class="sign-in-card w-full max-w-md">
                        <h1 class="sign-in-title">Forgot Password</h1>
                        <p class="text-gray-300 text-center mb-6">Enter your email address and we'll send you an OTP to reset your password.</p>

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
>>>>>>> 83faba04c87105623853f9cb40fd6c9b6e50a94a
                </div>
            </div>
        </div>
    </div>

    <script>
<<<<<<< HEAD
        const form = document.getElementById('forgotPasswordForm');
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const submitButton = document.getElementById('submitButton');
        const successMessage = document.getElementById('successMessage');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            emailError.classList.add('hidden');
            successMessage.classList.remove('show');
            submitButton.disabled = true;
            submitButton.textContent = 'Sending...';

            try {
                const response = await fetch('{{ route('password.email') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: emailInput.value.trim()
                    })
=======
        const emailInput = document.getElementById('email');
        const sendOtpButton = document.getElementById('sendOtpButton');
        const emailError = document.getElementById('emailError');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');

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
>>>>>>> 83faba04c87105623853f9cb40fd6c9b6e50a94a
                });

                const data = await response.json();

                if (!response.ok) {
<<<<<<< HEAD
                    emailError.textContent = data.message || 'Failed to send password reset link.';
                    emailError.classList.remove('hidden');
                } else {
                    successMessage.classList.add('show');
                    emailInput.value = '';
                    
                    setTimeout(() => {
                        successMessage.classList.remove('show');
                    }, 4000);
                }
            } catch (error) {
                emailError.textContent = 'An error occurred. Please try again.';
                emailError.classList.remove('hidden');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Send Link';
=======
                    emailError.textContent = data.message || 'Unable to send OTP. Please try again.';
                    emailError.classList.remove('hidden');
                    sendOtpButton.disabled = false;
                    sendOtpButton.textContent = 'Send OTP';
                    return;
                }

                // Redirect to OTP verification page
                window.location.href = '{{ route('verify-otp') }}?email=' + encodeURIComponent(email);
            } catch (error) {
                emailError.textContent = 'Unable to send OTP. Please try again.';
                emailError.classList.remove('hidden');
                sendOtpButton.disabled = false;
                sendOtpButton.textContent = 'Send OTP';
            }
        }

        sendOtpButton.addEventListener('click', function() {
            sendOtp();
        });

        // Enter key support
        emailInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendOtp();
>>>>>>> 83faba04c87105623853f9cb40fd6c9b6e50a94a
            }
        });
    </script>
</body>
</html>
