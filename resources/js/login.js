const rememberCheckbox = document.getElementById('rememberMe');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');

// Handle Remember Me checkbox
rememberCheckbox.addEventListener('change', function() {
    if (this.checked) {
        // Enable autocomplete for saving
        emailInput.autocomplete = 'email';
        passwordInput.autocomplete = 'current-password';
    } else {
        // Disable autocomplete to prevent saving
        emailInput.autocomplete = 'off';
        passwordInput.autocomplete = 'new-password';
    }
});

window.togglePassword = function(button) {
    const input = button.previousElementSibling;
    const type = input.type === 'password' ? 'text' : 'password';
    input.type = type;
};
