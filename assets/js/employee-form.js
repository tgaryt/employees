/**
 * File: assets/js/employee-form.js
 * JavaScript for employee add/edit form
 */

document.addEventListener('DOMContentLoaded', function() {
	// Initialize form tabs
	initFormTabs();
	
	// Initialize form validation
	initFormValidation();
	
	// Initialize file upload previews
	initFileUploads();
});

/**
 * Initialize form tabs
 */
function initFormTabs() {
	const tabs = document.querySelectorAll('[data-tab]');
	const contents = document.querySelectorAll('[data-tab-content]');
	
	tabs.forEach(tab => {
		tab.addEventListener('click', function(e) {
			e.preventDefault();
			
			const targetId = this.getAttribute('data-tab');
			
			// Update active tab
			tabs.forEach(t => {
				t.classList.remove('text-blue-600', 'border-blue-600');
				t.classList.add('text-gray-500', 'border-transparent');
			});
			this.classList.remove('text-gray-500', 'border-transparent');
			this.classList.add('text-blue-600', 'border-blue-600');
			
			// Show active tab content
			contents.forEach(content => {
				if (content.getAttribute('data-tab-content') === targetId) {
					content.classList.remove('hidden');
				} else {
					content.classList.add('hidden');
				}
			});
		});
	});
}

/**
 * Initialize form validation
 */
function initFormValidation() {
	const form = document.querySelector('form');
	const requiredFields = form.querySelectorAll('[required]');
	
	form.addEventListener('submit', function(e) {
		let isValid = true;
		
		// Check required fields
		requiredFields.forEach(field => {
			if (!field.value.trim()) {
				isValid = false;
				showError(field, 'This field is required');
			} else {
				clearError(field);
			}
		});
		
		// Email validation
		const emailField = document.getElementById('email');
		if (emailField && emailField.value.trim() && !isValidEmail(emailField.value.trim())) {
			isValid = false;
			showError(emailField, 'Please enter a valid email address');
		}
		
		// Numeric fields validation
		const numericFields = ['salary', 'work_hours'];
		numericFields.forEach(fieldId => {
			const field = document.getElementById(fieldId);
			if (field && field.value.trim() && isNaN(field.value.trim())) {
				isValid = false;
				showError(field, 'Please enter a valid number');
			}
		});
		
		// Date validation
		const dateField = document.getElementById('start_date');
		if (dateField && dateField.value.trim() && !isValidDate(dateField.value)) {
			isValid = false;
			showError(dateField, 'Please enter a valid date');
		}
		
		// File validation
		const fileFields = ['offer_letter', 'id_front', 'id_back'];
		fileFields.forEach(fieldId => {
			const field = document.getElementById(fieldId);
			if (field && field.files.length > 0) {
				const file = field.files[0];
				
				// Check file size (max 5MB)
				if (file.size > 5 * 1024 * 1024) {
					isValid = false;
					showError(field, 'File size should be less than 5MB');
				}
				
				// Check file type
				if (fieldId === 'offer_letter' && !file.type.includes('pdf')) {
					isValid = false;
					showError(field, 'Only PDF files are allowed');
				} else if ((fieldId === 'id_front' || fieldId === 'id_back') && 
						  !(file.type.includes('pdf') || file.type.includes('jpg') || file.type.includes('jpeg') || file.type.includes('png'))) {
					isValid = false;
					showError(field, 'Only PDF, JPG, or PNG files are allowed');
				}
			}
		});
		
		if (!isValid) {
			e.preventDefault();
			
			// Show error message at the top
			const formError = document.createElement('div');
			formError.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6';
			formError.id = 'form-error';
			formError.innerHTML = '<p>Please correct the errors below.</p>';
			
			// Remove existing error message if any
			const existingError = document.getElementById('form-error');
			if (existingError) {
				existingError.remove();
			}
			
			// Add error message at the top of the form
			form.insertBefore(formError, form.firstChild);
			
			// Scroll to the top
			window.scrollTo({ top: form.offsetTop - 100, behavior: 'smooth' });
		}
	});
	
	// Live validation for required fields
	requiredFields.forEach(field => {
		field.addEventListener('blur', function() {
			if (!this.value.trim()) {
				showError(this, 'This field is required');
			} else {
				clearError(this);
			}
		});
		
		field.addEventListener('input', function() {
			if (this.value.trim()) {
				clearError(this);
			}
		});
	});
	
	// Live validation for email
	const emailField = document.getElementById('email');
	if (emailField) {
		emailField.addEventListener('blur', function() {
			if (this.value.trim() && !isValidEmail(this.value.trim())) {
				showError(this, 'Please enter a valid email address');
			} else if (this.value.trim()) {
				clearError(this);
			}
		});
	}
}

/**
 * Show error message for a field
 */
function showError(field, message) {
	// Clear any existing error first
	clearError(field);
	
	// Add red border
	field.classList.add('border-red-500');
	
	// Create error message
	const errorElement = document.createElement('p');
	errorElement.className = 'text-red-500 text-xs mt-1';
	errorElement.id = `${field.id}-error`;
	errorElement.textContent = message;
	
	// Add error message after the field
	field.parentNode.appendChild(errorElement);
}

/**
 * Clear error message for a field
 */
function clearError(field) {
	// Remove red border
	field.classList.remove('border-red-500');
	
	// Remove error message
	const errorElement = document.getElementById(`${field.id}-error`);
	if (errorElement) {
		errorElement.remove();
	}
}

/**
 * Check if an email is valid
 */
function isValidEmail(email) {
	const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	return regex.test(email);
}

/**
 * Check if a date is valid
 */
function isValidDate(dateString) {
	const regex = /^\d{4}-\d{2}-\d{2}$/;
	if (!regex.test(dateString)) return false;
	
	const date = new Date(dateString);
	return date instanceof Date && !isNaN(date);
}

/**
 * Initialize file upload previews
 */
function initFileUploads() {
	const fileInputs = document.querySelectorAll('input[type="file"]');
	
	fileInputs.forEach(input => {
		// Create preview container
		const previewContainer = document.createElement('div');
		previewContainer.className = 'mt-2 hidden';
		previewContainer.id = `${input.id}-preview-container`;
		
		// Add preview container after the input
		input.parentNode.insertBefore(previewContainer, input.nextSibling);
		
		// Add change event to input
		input.addEventListener('change', function() {
			const container = document.getElementById(`${this.id}-preview-container`);
			
			if (this.files.length > 0) {
				const file = this.files[0];
				
				// Show file name
				container.innerHTML = `
					<div class="flex items-center p-2 bg-gray-100 rounded">
						<i class="fas ${file.type.includes('pdf') ? 'fa-file-pdf text-red-500' : 'fa-file-image text-blue-500'} mr-2"></i>
						<span class="text-sm text-gray-700">${file.name}</span>
						<span class="text-xs text-gray-500 ml-2">(${formatFileSize(file.size)})</span>
					</div>
				`;
				container.classList.remove('hidden');
			} else {
				container.innerHTML = '';
				container.classList.add('hidden');
			}
		});
	});
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
	if (bytes < 1024) return bytes + ' bytes';
	else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
	else return (bytes / 1048576).toFixed(1) + ' MB';
}