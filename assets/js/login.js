document.addEventListener('DOMContentLoaded', function() {
	// Form validation
	const loginForm = document.querySelector('form');
	
	if (loginForm) {
		loginForm.addEventListener('submit', function(e) {
			const username = document.getElementById('username').value.trim();
			const password = document.getElementById('password').value.trim();
			
			let isValid = true;
			
			// Simple client-side validation
			if (username === '') {
				showError('username', 'Username is required');
				isValid = false;
			} else {
				clearError('username');
			}
			
			if (password === '') {
				showError('password', 'Password is required');
				isValid = false;
			} else {
				clearError('password');
			}
			
			if (!isValid) {
				e.preventDefault();
			}
		});
	}
	
	// Show error message
	function showError(fieldId, message) {
		const field = document.getElementById(fieldId);
		const errorElement = document.createElement('p');
		
		// Remove any existing error first
		clearError(fieldId);
		
		errorElement.className = 'text-red-500 text-xs mt-1';
		errorElement.id = `${fieldId}-error`;
		errorElement.textContent = message;
		
		field.classList.add('border-red-500');
		field.parentNode.appendChild(errorElement);
	}
	
	// Clear error message
	function clearError(fieldId) {
		const field = document.getElementById(fieldId);
		const errorElement = document.getElementById(`${fieldId}-error`);
		
		if (errorElement) {
			errorElement.remove();
		}
		
		field.classList.remove('border-red-500');
	}
	
	// Remember me functionality (using localStorage)
	const rememberCheckbox = document.getElementById('remember');
	const usernameField = document.getElementById('username');
	
	// Load saved username if remember me was checked
	if (localStorage.getItem('ez_ad_remember') === 'true') {
		usernameField.value = localStorage.getItem('ez_ad_username') || '';
		rememberCheckbox.checked = true;
	}
	
	// Save username if remember me is checked
	loginForm.addEventListener('submit', function() {
		if (rememberCheckbox.checked) {
			localStorage.setItem('ez_ad_remember', 'true');
			localStorage.setItem('ez_ad_username', usernameField.value);
		} else {
			localStorage.removeItem('ez_ad_remember');
			localStorage.removeItem('ez_ad_username');
		}
	});
});