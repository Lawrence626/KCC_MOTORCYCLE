<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCC - Forgot Password</title>
    @vite(['resources/css/app.css', 'resources/css/login.css'])
    <style>
        .step-indicator {
            transition: all 0.3s ease;
        }
        .step-indicator.active {
            background-color: #14b8a6;
            color: white;
            border-color: #14b8a6;
        }
        .step-indicator.completed {
            background-color: #10b981;
            color: white;
            border-color: #10b981;
        }
        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
        }
        .otp-input {
            text-align: center;
            letter-spacing: 0.5em;
            font-size: 24px;
            font-family: monospace;
        }
        .password-req {
            font-size: 12px;
            color: #ef4444;
        }
        .password-req.valid {
            color: #10b981;
        }
        .alert-message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }
        #alert-container {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            width: 90%;
            max-width: 400px;
        }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <!-- Alert Container -->
    <div id="alert-container"></div>

    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white">KCC Motorcycle</h1>
            <p class="text-gray-300 mt-2">Reset Your Password</p>
        </div>

        <!-- Step Indicators -->
        <div class="flex justify-center mb-6">
            <div class="flex items-center gap-2">
                <div id="step1-indicator" class="step-indicator active w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center text-sm font-semibold text-gray-400">1</div>
                <div class="w-12 h-0.5 bg-gray-300" id="line1"></div>
                <div id="step2-indicator" class="step-indicator w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center text-sm font-semibold text-gray-400">2</div>
                <div class="w-12 h-0.5 bg-gray-300" id="line2"></div>
                <div id="step3-indicator" class="step-indicator w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center text-sm font-semibold text-gray-400">3</div>
            </div>
        </div>

        <!-- Step 1: Enter Email -->
        <div id="step1" class="step-content active sign-in-card">
            <p class="text-gray-300 mb-6 text-sm">We'll send a 6-digit code to your email to reset your password.</p>
            
            <form id="email-form" class="space-y-6">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="relative">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            class="form-input"
                            autocomplete="off"
                            required
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    id="send-code-btn"
                    class="login-button"
                >
                    Send Reset Code
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-sm">Back to Login</a>
            </div>
        </div>

        <!-- Step 2: Enter Code -->
        <div id="step2" class="step-content sign-in-card">
            <p class="text-gray-300 mb-2 text-sm">Enter the 6-digit code sent to:</p>
            <p id="display-email" class="text-white font-semibold mb-6 text-sm"></p>
            
            <form id="code-form" class="space-y-6">
                @csrf

                <div class="form-group">
                    <label for="code">Verification Code</label>
                    <div class="relative">
                        <input
                            type="text"
                            id="code"
                            name="code"
                            placeholder="000000"
                            maxlength="6"
                            pattern="\d{6}"
                            class="form-input otp-input"
                            autocomplete="off"
                            required
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    id="verify-code-btn"
                    class="login-button"
                >
                    Verify Code
                </button>

                <button
                    type="button"
                    id="resend-code-btn"
                    class="w-full border border-gray-300 text-gray-300 py-3 rounded-lg font-semibold hover:bg-gray-700 transition"
                >
                    Resend Code
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <button type="button" id="back-to-email-btn" class="text-gray-300 hover:text-white text-sm">Change email</button>
            </div>
        </div>

        <!-- Step 3: New Password -->
        <div id="step3" class="step-content sign-in-card">
            <p class="text-gray-300 mb-6 text-sm">Create a new password for your account.</p>
            
            <form id="password-form" class="space-y-6">
                @csrf

                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="new-password"
                            name="password"
                            placeholder="Enter new password"
                            class="form-input"
                            autocomplete="off"
                            required
                        >
                        <button type="button" class="eye-icon-button" onclick="togglePassword('new-password')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5,12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="confirm-password"
                            name="password_confirmation"
                            placeholder="Confirm new password"
                            class="form-input"
                            autocomplete="off"
                            required
                        >
                        <button type="button" class="eye-icon-button" onclick="togglePassword('confirm-password')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5,12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Password Requirements -->
                <div id="password-requirements" class="bg-red-900/30 border border-red-500/30 rounded-lg p-3">
                    <p class="text-xs font-semibold text-red-400 mb-2">Password must contain:</p>
                    <ul class="text-xs space-y-1">
                        <li id="req-length" class="password-req flex items-center gap-2">
                            <span>✕</span> 12+ characters
                        </li>
                        <li id="req-lowercase" class="password-req flex items-center gap-2">
                            <span>✕</span> Lowercase letter (a-z)
                        </li>
                        <li id="req-uppercase" class="password-req flex items-center gap-2">
                            <span>✕</span> Uppercase letter (A-Z)
                        </li>
                        <li id="req-number" class="password-req flex items-center gap-2">
                            <span>✕</span> Number (0-9)
                        </li>
                        <li id="req-special" class="password-req flex items-center gap-2">
                            <span>✕</span> Special character (@ $ ! % * # ?)
                        </li>
                    </ul>
                </div>

                <button
                    type="submit"
                    id="reset-password-btn"
                    class="login-button"
                >
                    Reset Password
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <button type="button" id="back-to-code-btn" class="text-gray-300 hover:text-white text-sm">Back to code verification</button>
            </div>
        </div>

        <!-- Success Message -->
        <div id="success-message" class="hidden sign-in-card text-center">
            <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-white mb-2">Password Reset Successful!</h2>
            <p class="text-gray-300 mb-6 text-sm">Your password has been reset successfully. You can now login with your new password.</p>
            <a href="{{ route('login') }}" 
                class="login-button inline-block text-center">
                Go to Login
            </a>
        </div>
    </div>

    <script>
        let currentEmail = '';
        let currentCode = '';

        // Show alert
        function showAlert(message, type = 'error') {
            const container = document.getElementById('alert-container');
            const alertClass = type === 'success' 
                ? 'bg-green-50 border-green-200 text-green-800' 
                : 'bg-red-50 border-red-200 text-red-800';
            
            container.innerHTML = `
                <div class="rounded-lg border p-4 ${alertClass}">
                    <p class="text-sm">${message}</p>
                </div>
            `;
            
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }

        // Toggle password visibility
        window.togglePassword = function(inputId) {
            const input = document.getElementById(inputId);
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
        };

        // Update step indicators
        function updateStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            
            // Show current step
            document.getElementById(`step${step}`).classList.add('active');
            
            // Update indicators
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById(`step${i}-indicator`);
                indicator.classList.remove('active', 'completed');
                
                if (i < step) {
                    indicator.classList.add('completed');
                    indicator.innerHTML = '✓';
                } else if (i === step) {
                    indicator.classList.add('active');
                    indicator.innerHTML = i;
                } else {
                    indicator.innerHTML = i;
                }
            }
            
            // Update lines
            document.getElementById('line1').style.backgroundColor = step > 1 ? '#10b981' : '#cbd5e1';
            document.getElementById('line2').style.backgroundColor = step > 2 ? '#10b981' : '#cbd5e1';
        }

        // Step 1: Send code
        document.getElementById('email-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const btn = document.getElementById('send-code-btn');
            
            btn.disabled = true;
            btn.textContent = 'Sending...';
            
            try {
                const response = await fetch('{{ route('password.send-code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ email }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    currentEmail = email;
                    document.getElementById('display-email').textContent = email;
                    
                    // Show development code if available
                    if (data.dev_code) {
                        showAlert(`Development mode: Your code is ${data.dev_code}`, 'success');
                    } else {
                        showAlert(data.message, 'success');
                    }
                    
                    updateStep(2);
                } else {
                    showAlert(data.message || 'Failed to send code');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.');
            }
            
            btn.disabled = false;
            btn.textContent = 'Send Reset Code';
        });

        // Step 2: Verify code
        document.getElementById('code-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const code = document.getElementById('code').value;
            const btn = document.getElementById('verify-code-btn');
            
            btn.disabled = true;
            btn.textContent = 'Verifying...';
            
            try {
                const response = await fetch('{{ route('password.verify-code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ email: currentEmail, code }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    currentCode = code;
                    showAlert(data.message, 'success');
                    updateStep(3);
                } else {
                    showAlert(data.message || 'Invalid code');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.');
            }
            
            btn.disabled = false;
            btn.textContent = 'Verify Code';
        });

        // Resend code
        document.getElementById('resend-code-btn').addEventListener('click', async function() {
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Sending...';
            
            try {
                const response = await fetch('{{ route('password.send-code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ email: currentEmail }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    if (data.dev_code) {
                        showAlert(`Development mode: Your code is ${data.dev_code}`, 'success');
                    } else {
                        showAlert(data.message, 'success');
                    }
                } else {
                    showAlert(data.message || 'Failed to resend code');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.');
            }
            
            btn.disabled = false;
            btn.textContent = 'Resend Code';
        });

        // Back to email
        document.getElementById('back-to-email-btn').addEventListener('click', function() {
            updateStep(1);
        });

        // Back to code
        document.getElementById('back-to-code-btn').addEventListener('click', function() {
            updateStep(2);
        });

        // Password requirements validation
        const passwordInput = document.getElementById('new-password');
        const requirements = {
            length: /.{12,}/,
            lowercase: /[a-z]/,
            uppercase: /[A-Z]/,
            number: /[0-9]/,
            special: /[@$!%*#?]/
        };

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            Object.keys(requirements).forEach(key => {
                const req = document.getElementById(`req-${key}`);
                const isValid = requirements[key].test(password);
                
                if (isValid) {
                    req.querySelector('span').textContent = '✓';
                    req.querySelector('span').classList.remove('text-red-500');
                    req.querySelector('span').classList.add('text-green-600');
                    req.classList.remove('text-red-600');
                    req.classList.add('text-green-600');
                } else {
                    req.querySelector('span').textContent = '✕';
                    req.querySelector('span').classList.remove('text-green-600');
                    req.querySelector('span').classList.add('text-red-500');
                    req.classList.remove('text-green-600');
                    req.classList.add('text-red-600');
                }
            });
        });

        // Step 3: Reset password
        document.getElementById('password-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const password = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const btn = document.getElementById('reset-password-btn');
            
            if (password !== confirmPassword) {
                showAlert('Passwords do not match');
                return;
            }
            
            // Validate password requirements
            const allValid = Object.values(requirements).every(req => req.test(password));
            if (!allValid) {
                showAlert('Password does not meet all requirements');
                return;
            }
            
            btn.disabled = true;
            btn.textContent = 'Resetting...';
            
            try {
                const response = await fetch('{{ route('password.reset.code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ 
                        email: currentEmail, 
                        code: currentCode, 
                        password,
                        password_confirmation: confirmPassword
                    }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Hide all steps and show success
                    document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
                    document.getElementById('success-message').classList.remove('hidden');
                    
                    // Hide step indicators
                    document.querySelector('.flex.justify-center.mb-8').style.display = 'none';
                } else {
                    showAlert(data.message || 'Failed to reset password');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.');
            }
            
            btn.disabled = false;
            btn.textContent = 'Reset Password';
        });
    </script>
</body>
</html>
