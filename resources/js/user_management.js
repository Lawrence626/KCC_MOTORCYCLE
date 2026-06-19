/**
 * User Management Module
 * Handles user CRUD operations, modal management, and form validation
 */

(function(){
    // === DOM Elements ===
    const openBtn = document.getElementById('openAddUserModal');
    const modal = document.getElementById('addUserModal');
    const closeBtn = document.getElementById('closeAddUserModal');
    const cancelBtn = document.getElementById('cancelAddUser');
    const successAlert = document.getElementById('pageSuccessAlert');
    const addUserForm = modal ? modal.querySelector('form') : null;

    const editModal = document.getElementById('editUserModal');
    const editForm = document.getElementById('editUserForm');
    const editCloseBtn = document.getElementById('closeEditUserModal');
    const editCancelBtn = document.getElementById('cancelEditUser');
    const editName = document.getElementById('edit_name');
    const editEmail = document.getElementById('edit_email');
    const editRole = document.getElementById('edit_role');
    const editContact = document.getElementById('edit_contact');
    const editAddress = document.getElementById('edit_address');
    const editAge = document.getElementById('edit_age');
    const editGender = document.getElementById('edit_gender');
    const editPassword = document.getElementById('edit_password');

    const addPasswordInput = document.getElementById('add_password');
    const addPasswordConfirm = document.getElementById('add_password_confirm');
    const addPasswordReqs = document.getElementById('add_password_requirements');
    const addPasswordMatch = document.getElementById('add_password_match');
    const addUserSubmitBtn = document.getElementById('addUserSubmitBtn');

    const editPasswordInput = document.getElementById('edit_password');
    const editPasswordReqs = document.getElementById('edit_password_requirements');

    const managementRoute = document.querySelector('body').getAttribute('data-management-route') || getManagementRoute();
    const userListWrapper = document.getElementById('userListWrapper');

    // === Helper Functions ===
    function getManagementRoute() {
        // Try to extract from page data or use default
        if (document.body.getAttribute('data-archived') === 'true') {
            return '{{ route("user.management.archived") }}';
        }
        return '{{ route("user.management") }}';
    }

    // === Modal Management ===
    function openAddModal() {
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeAddModal() {
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        if (addUserForm) addUserForm.reset();
    }

    function openEditModal() {
        if (!editModal) return;
        editModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEditModal() {
        if (!editModal) return;
        editModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        if (editForm) editForm.reset();
        const confirmWrapper = document.getElementById('edit_password_confirm_wrapper');
        if (confirmWrapper) confirmWrapper.style.display = 'none';
        if (editPasswordReqs) editPasswordReqs.classList.add('hidden');
    }

    // === Add Modal Event Listeners ===
    openBtn?.addEventListener('click', openAddModal);
    closeBtn?.addEventListener('click', closeAddModal);
    cancelBtn?.addEventListener('click', closeAddModal);

    modal?.addEventListener('click', function(e) {
        if (e.target && e.target.getAttribute && e.target.getAttribute('data-action') === 'close-modal') {
            closeAddModal();
        }
    });

    // === Edit Modal Event Listeners ===
    editCloseBtn?.addEventListener('click', closeEditModal);
    editCancelBtn?.addEventListener('click', closeEditModal);
    editModal?.addEventListener('click', function(e) {
        if (e.target && e.target.getAttribute && e.target.getAttribute('data-action') === 'close-modal') {
            closeEditModal();
        }
    });

    // === Keyboard Shortcuts ===
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
        }
    });

    // === Success Alert Auto-hide ===
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.transition = 'opacity 0.3s ease';
            successAlert.style.opacity = '0';
            setTimeout(function() {
                successAlert.remove();
            }, 300);
        }, 3000);
    }

    // === Password Validation ===
    function validatePasswordRequirements(password) {
        return {
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[@$!%*#?&^()\-]/.test(password),
            length: password.length >= 12
        };
    }

    function updatePasswordRequirements(requirementsEl, password) {
        if (!requirementsEl) return;
        const reqs = validatePasswordRequirements(password);

        requirementsEl.querySelectorAll('[data-requirement]').forEach(function(item) {
            const requirement = item.getAttribute('data-requirement');
            const isMet = reqs[requirement];
            item.querySelector('span:first-child').textContent = isMet ? '✓' : '✕';
            item.querySelector('span:first-child').className = isMet ? 'text-green-500 text-xs' : 'text-red-500 text-xs';
            item.querySelector('span:last-child').className = isMet ? 'text-green-600 text-[10px]' : 'text-red-600 text-[10px]';
        });
    }

    function isPasswordValid(password) {
        const reqs = validatePasswordRequirements(password);
        return reqs.lowercase && reqs.uppercase && reqs.number && reqs.special && reqs.length;
    }

    // === Password Toggle ===
    function setupPasswordToggle() {
        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const targetSelector = this.getAttribute('data-target');
                const input = document.querySelector(targetSelector);
                if (input) {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    this.classList.toggle('text-teal-600', !isPassword);
                }
            });
        });
    }

    // === Add User Form Validation ===
    function validatePasswordMatch() {
        if (!addPasswordInput || !addPasswordConfirm || !addPasswordMatch) return;

        const password = addPasswordInput.value;
        const confirm = addPasswordConfirm.value;

        if (!confirm) {
            addPasswordMatch.classList.add('hidden');
            updateAddUserSubmitButton();
            return;
        }

        addPasswordMatch.classList.remove('hidden');
        if (password === confirm) {
            addPasswordMatch.className = 'text-xs mt-2 p-2 rounded-lg bg-green-50 border border-green-200 text-green-700';
            addPasswordMatch.textContent = '✓ Passwords match';
        } else {
            addPasswordMatch.className = 'text-xs mt-2 p-2 rounded-lg bg-red-50 border border-red-200 text-red-700';
            addPasswordMatch.textContent = '✕ Passwords do not match';
        }
        updateAddUserSubmitButton();
    }

    function updateAddUserSubmitButton() {
        if (!addUserSubmitBtn || !addPasswordInput || !addPasswordConfirm) return;

        const password = addPasswordInput.value;
        const confirm = addPasswordConfirm.value;
        const passwordsValid = isPasswordValid(password);
        const passwordsMatch = password === confirm && password.length > 0;

        if (passwordsValid && passwordsMatch) {
            addUserSubmitBtn.disabled = false;
            addUserSubmitBtn.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
        } else {
            addUserSubmitBtn.disabled = true;
            addUserSubmitBtn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed');
        }
    }

    // Add user password validation listeners
    if (addPasswordInput) {
        addPasswordInput.addEventListener('input', function() {
            updatePasswordRequirements(addPasswordReqs, this.value);
            if (addPasswordConfirm && addPasswordConfirm.value) {
                validatePasswordMatch();
            }
            updateAddUserSubmitButton();
        });
    }

    if (addPasswordConfirm) {
        addPasswordConfirm.addEventListener('input', function() {
            if (this.value) {
                validatePasswordMatch();
            } else {
                addPasswordMatch.classList.add('hidden');
                updateAddUserSubmitButton();
            }
        });
    }

    // Edit user password validation
    if (editPasswordInput) {
        editPasswordInput.addEventListener('input', function() {
            const confirmWrapper = document.getElementById('edit_password_confirm_wrapper');
            if (this.value) {
                editPasswordReqs.classList.remove('hidden');
                if (confirmWrapper) confirmWrapper.style.display = 'block';
                updatePasswordRequirements(editPasswordReqs, this.value);
            } else {
                editPasswordReqs.classList.add('hidden');
                if (confirmWrapper) confirmWrapper.style.display = 'none';
                document.getElementById('edit_password_confirm').value = '';
            }
        });
    }

    // Initialize submit button as disabled
    if (addUserSubmitBtn) {
        addUserSubmitBtn.disabled = true;
    }

    // === Form Submissions ===
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            const password = addPasswordInput.value;
            const confirm = addPasswordConfirm.value;

            if (!isPasswordValid(password)) {
                e.preventDefault();
                alert('Password does not meet security requirements. Please ensure it contains:\n- At least one lowercase letter\n- At least one uppercase letter\n- At least one number\n- At least one special character (e.g., @ $ ! % * # ? & ^ ( ) -)\n- At least 12 characters');
                return false;
            }

            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match.');
                return false;
            }
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const password = editPasswordInput.value;
            const userId = this.dataset.userId;

            // Only validate if password is being changed
            if (password) {
                if (!isPasswordValid(password)) {
                    alert('Password does not meet security requirements. Please ensure it contains:\n- At least one lowercase letter\n- At least one uppercase letter\n- At least one number\n- At least one special character (e.g., @ $ ! % * # ? & ^ ( ) -)\n- At least 12 characters');
                    return false;
                }
            }

            // Set form action to correct route
            this.action = '/users/' + userId;
            this.method = 'POST'; // Will be overridden by hidden method field

            // Submit the form
            this.submit();
        });
    }

    // === Edit User Management ===
    function attachEditButtons() {
        document.querySelectorAll('.editUserBtn').forEach(function(button) {
            button.removeEventListener('click', handleEditButtonClick);
            button.addEventListener('click', handleEditButtonClick);
        });
    }

    function handleEditButtonClick() {
        const user = JSON.parse(this.getAttribute('data-user'));
        if (!user) return;

        // Store user ID on form for submission
        editForm.dataset.userId = user.id;
        editName.value = user.name || '';
        editEmail.value = user.email || '';
        editRole.value = user.role || 'admin';
        editContact.value = user.contact || '';
        editAddress.value = user.address || '';
        editAge.value = user.age || '';
        editGender.value = user.gender || '';
        editPassword.value = '';

        openEditModal();
    }

    // === User List Management ===
    function refreshBindings() {
        const searchInput = document.getElementById('userSearchInput');
        const searchForm = document.getElementById('userSearchForm');

        if (searchForm) {
            searchForm.addEventListener('submit', function(event) {
                event.preventDefault();
            });
        }

        if (searchInput && searchForm) {
            let searchTimer = null;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    const formData = new FormData(searchForm);
                    const query = new URLSearchParams(formData).toString();
                    const url = managementRoute + (query ? '?' + query : '');
                    updateUserList(url, true);
                }, 450);
            });
        }

        attachPaginationListener();
        attachEditButtons();
        setupPasswordToggle();
    }

    function updateUserList(url, replaceHistory) {
        if (!userListWrapper) {
            window.location.href = url;
            return;
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(function(html) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newWrapper = doc.getElementById('userListWrapper');
            if (newWrapper) {
                userListWrapper.innerHTML = newWrapper.innerHTML;
                if (replaceHistory) {
                    window.history.pushState({}, '', url);
                }
                refreshBindings();
            } else {
                window.location.href = url;
            }
        })
        .catch(function() {
            window.location.href = url;
        });
    }

    function attachPaginationListener() {
        if (!userListWrapper) return;
        userListWrapper.querySelectorAll('.pagination a').forEach(function(link) {
            link.removeEventListener('click', handlePaginationClick);
            link.addEventListener('click', handlePaginationClick);
        });
    }

    function handlePaginationClick(event) {
        event.preventDefault();
        const href = this.getAttribute('href');
        if (href) {
            updateUserList(href, true);
        }
    }

    // === Initialization ===
    refreshBindings();

    window.addEventListener('popstate', function() {
        updateUserList(window.location.href, false);
    });
})();
