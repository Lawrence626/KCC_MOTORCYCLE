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

window.togglePassword = function(button){

    const input = button.previousElementSibling;
    const svg = button.querySelector("svg");

    if(input.type === "password"){

        input.type = "text";

        button.classList.add("active");

        button.setAttribute("aria-label","Hide password");

        svg.innerHTML = `
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M1.5 12S5.5 4.5 12 4.5 22.5 12 22.5 12 18.5 19.5 12 19.5 1.5 12 1.5 12Z"/>

            <circle cx="12"
                    cy="12"
                    r="3"
                    stroke="currentColor"
                    stroke-width="2"
                    fill="none"/>
        `;

    }else{

        input.type = "password";

        button.classList.remove("active");

        button.setAttribute("aria-label","Show password");

        svg.innerHTML = `
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 3l18 18"/>

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10.58 10.58a2 2 0 102.83 2.83"/>

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a11.83 11.83 0 01-3.05 4.36"/>

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6.23 6.23A11.83 11.83 0 001 12c1.73 3.89 6 7 11 7a10.94 10.94 0 005.77-1.68"/>
        `;

    }

}