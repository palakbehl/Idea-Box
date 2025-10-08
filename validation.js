// Form Validation JavaScript

// Utility functions
function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

function hideError(elementId) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
}

function clearAllErrors() {
    const errorElements = document.querySelectorAll('.error-text');
    errorElements.forEach(element => {
        element.textContent = '';
        element.style.display = 'none';
    });
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePassword(password) {
    return password.length >= 6;
}

function validateName(name) {
    return name.trim().length >= 2;
}

// Registration Form Validation
function validateRegistrationForm() {
    clearAllErrors();
    let isValid = true;
    
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Name validation
    if (!name) {
        showError('name-error', 'Name is required');
        isValid = false;
    } else if (!validateName(name)) {
        showError('name-error', 'Name must be at least 2 characters long');
        isValid = false;
    }
    
    // Email validation
    if (!email) {
        showError('email-error', 'Email is required');
        isValid = false;
    } else if (!validateEmail(email)) {
        showError('email-error', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Password validation
    if (!password) {
        showError('password-error', 'Password is required');
        isValid = false;
    } else if (!validatePassword(password)) {
        showError('password-error', 'Password must be at least 6 characters long');
        isValid = false;
    }
    
    // Confirm password validation
    if (!confirmPassword) {
        showError('confirm-password-error', 'Please confirm your password');
        isValid = false;
    } else if (password !== confirmPassword) {
        showError('confirm-password-error', 'Passwords do not match');
        isValid = false;
    }
    
    return isValid;
}

// Login Form Validation
function validateLoginForm() {
    clearAllErrors();
    let isValid = true;
    
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    // Email validation
    if (!email) {
        showError('email-error', 'Email is required');
        isValid = false;
    } else if (!validateEmail(email)) {
        showError('email-error', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Password validation
    if (!password) {
        showError('password-error', 'Password is required');
        isValid = false;
    }
    
    return isValid;
}

// Profile Form Validation
function validateProfileForm() {
    clearAllErrors();
    let isValid = true;
    
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    
    // Name validation
    if (!name) {
        showError('name-error', 'Name is required');
        isValid = false;
    } else if (!validateName(name)) {
        showError('name-error', 'Name must be at least 2 characters long');
        isValid = false;
    }
    
    // Email validation
    if (!email) {
        showError('email-error', 'Email is required');
        isValid = false;
    } else if (!validateEmail(email)) {
        showError('email-error', 'Please enter a valid email address');
        isValid = false;
    }
    
    return isValid;
}

// Password Change Form Validation
function validatePasswordChangeForm() {
    clearAllErrors();
    let isValid = true;
    
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Current password validation
    if (!currentPassword) {
        showError('current-password-error', 'Current password is required');
        isValid = false;
    }
    
    // New password validation
    if (!newPassword) {
        showError('new-password-error', 'New password is required');
        isValid = false;
    } else if (!validatePassword(newPassword)) {
        showError('new-password-error', 'New password must be at least 6 characters long');
        isValid = false;
    }
    
    // Confirm password validation
    if (!confirmPassword) {
        showError('confirm-password-error', 'Please confirm your new password');
        isValid = false;
    } else if (newPassword !== confirmPassword) {
        showError('confirm-password-error', 'New passwords do not match');
        isValid = false;
    }
    
    return isValid;
}

// Idea Form Validation
function validateIdeaForm() {
    clearAllErrors();
    let isValid = true;
    
    const title = document.getElementById('title').value.trim();
    const description = document.getElementById('description').value.trim();
    const categoryId = document.getElementById('category').value;
    const fileInput = document.getElementById('file');
    
    // Title validation
    if (!title) {
        showError('title-error', 'Title is required');
        isValid = false;
    } else if (title.length > 200) {
        showError('title-error', 'Title must be 200 characters or less');
        isValid = false;
    }
    
    // Description validation
    if (!description) {
        showError('description-error', 'Description is required');
        isValid = false;
    } else if (description.length < 10) {
        showError('description-error', 'Description must be at least 10 characters long');
        isValid = false;
    }
    
    // Category validation
    if (!categoryId) {
        showError('category-error', 'Please select a category');
        isValid = false;
    }
    
    // File validation (if file is selected)
    if (fileInput && fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
                              'application/pdf', 'application/msword', 
                              'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!allowedTypes.includes(file.type)) {
            showError('file-error', 'File type not allowed. Please use JPG, PNG, GIF, PDF, DOC, or DOCX files.');
            isValid = false;
        } else if (file.size > maxSize) {
            showError('file-error', 'File size must be less than 5MB');
            isValid = false;
        }
    }
    
    return isValid;
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function() {
    // Email field real-time validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const email = this.value.trim();
            const errorId = this.id + '-error';
            
            if (email && !validateEmail(email)) {
                showError(errorId, 'Please enter a valid email address');
            } else {
                hideError(errorId);
            }
        });
    });
    
    // Password field real-time validation
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        if (input.name === 'password' || input.name === 'new_password') {
            input.addEventListener('blur', function() {
                const password = this.value;
                const errorId = this.id + '-error';
                
                if (password && !validatePassword(password)) {
                    showError(errorId, 'Password must be at least 6 characters long');
                } else {
                    hideError(errorId);
                }
            });
        }
    });
    
    // Confirm password real-time validation
    const confirmPasswordInputs = document.querySelectorAll('input[name="confirm_password"]');
    confirmPasswordInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const confirmPassword = this.value;
            const passwordInput = document.querySelector('input[name="password"], input[name="new_password"]');
            const password = passwordInput ? passwordInput.value : '';
            const errorId = this.id + '-error';
            
            if (confirmPassword && password !== confirmPassword) {
                showError(errorId, 'Passwords do not match');
            } else {
                hideError(errorId);
            }
        });
    });
    
    // Name field real-time validation
    const nameInputs = document.querySelectorAll('input[name="name"]');
    nameInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const name = this.value.trim();
            const errorId = this.id + '-error';
            
            if (name && !validateName(name)) {
                showError(errorId, 'Name must be at least 2 characters long');
            } else {
                hideError(errorId);
            }
        });
    });
    
    // Title character counter
    const titleInput = document.getElementById('title');
    if (titleInput) {
        const maxLength = 200;
        titleInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            // Find or create character counter
            let counter = this.parentNode.querySelector('.char-counter');
            if (!counter) {
                counter = document.createElement('small');
                counter.className = 'char-counter';
                this.parentNode.appendChild(counter);
            }
            
            counter.textContent = `${remaining} characters remaining`;
            counter.style.color = remaining < 20 ? '#e74c3c' : '#7f8c8d';
            
            if (currentLength > maxLength) {
                this.value = this.value.substring(0, maxLength);
                counter.textContent = '0 characters remaining';
            }
        });
    }
    
    // File input validation
    const fileInput = document.getElementById('file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            const errorId = this.id + '-error';
            
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
                                      'application/pdf', 'application/msword', 
                                      'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (!allowedTypes.includes(file.type)) {
                    showError(errorId, 'File type not allowed. Please use JPG, PNG, GIF, PDF, DOC, or DOCX files.');
                    this.value = '';
                } else if (file.size > maxSize) {
                    showError(errorId, 'File size must be less than 5MB');
                    this.value = '';
                } else {
                    hideError(errorId);
                }
            }
        });
    }
});

// Form submission handling with loading states
function handleFormSubmission(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    // Find submit button within the form
    const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
    if (!submitButton) return;

    form.addEventListener('submit', function() {
        submitButton.disabled = true;
        if ('textContent' in submitButton) {
            submitButton.setAttribute('data-original-text', submitButton.textContent);
            submitButton.textContent = 'Please wait...';
        } else if ('value' in submitButton) {
            submitButton.setAttribute('data-original-text', submitButton.value);
            submitButton.value = 'Please wait...';
        }

        // Re-enable button after 10 seconds to prevent permanent lock
        setTimeout(() => {
            submitButton.disabled = false;
            const original = submitButton.getAttribute('data-original-text');
            if ('textContent' in submitButton) {
                submitButton.textContent = original || 'Submit';
            } else if ('value' in submitButton) {
                submitButton.value = original || 'Submit';
            }
        }, 10000);
    });
}

// Initialize form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    handleFormSubmission('registerForm');
    handleFormSubmission('loginForm');
    handleFormSubmission('profileForm');
    handleFormSubmission('passwordForm');
    handleFormSubmission('ideaForm');
    handleFormSubmission('commentForm');
});

// Auto-hide messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const messages = document.querySelectorAll('.success, .error');
    messages.forEach(message => {
        if (!message.classList.contains('permanent')) {
            setTimeout(() => {
                message.style.opacity = '0';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 300);
            }, 5000);
        }
    });
});

// Smooth scroll for anchor links
document.addEventListener('DOMContentLoaded', function() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Search form auto-submit on category/sort change
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        const selects = searchForm.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                searchForm.submit();
            });
        });
    }
});

// Dynamic loading indicator for AJAX requests
function showLoadingIndicator() {
    const indicator = document.createElement('div');
    indicator.id = 'loading-indicator';
    indicator.innerHTML = '<div class="loading-spinner"></div><p>Loading...</p>';
    indicator.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        color: white;
    `;
    document.body.appendChild(indicator);
}

function hideLoadingIndicator() {
    const indicator = document.getElementById('loading-indicator');
    if (indicator) {
        indicator.remove();
    }
}

// Add loading spinner CSS
const loadingCSS = `
.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top: 4px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
`;

// Inject loading CSS
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = loadingCSS;
    document.head.appendChild(style);
});

// Export validation functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validateRegistrationForm,
        validateLoginForm,
        validateProfileForm,
        validatePasswordChangeForm,
        validateIdeaForm,
        validateEmail,
        validatePassword,
        validateName,
        showError,
        hideError,
        clearAllErrors
    };
}