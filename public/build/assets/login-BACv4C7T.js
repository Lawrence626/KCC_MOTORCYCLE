var e=document.getElementById(`rememberMe`),t=document.getElementById(`email`),n=document.getElementById(`password`);e.addEventListener(`change`,function(){this.checked?(t.autocomplete=`email`,n.autocomplete=`current-password`):(t.autocomplete=`off`,n.autocomplete=`new-password`)}),window.togglePassword=function(e){let t=e.previousElementSibling,n=e.querySelector(`svg`);t.type===`password`?(t.type=`text`,e.classList.add(`active`),e.setAttribute(`aria-label`,`Hide password`),n.innerHTML=`
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
        `):(t.type=`password`,e.classList.remove(`active`),e.setAttribute(`aria-label`,`Show password`),n.innerHTML=`
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
        `)};