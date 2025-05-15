document.addEventListener('DOMContentLoaded', function() {
	// Initialize tooltips
	initTooltips();
	
	// Initialize modals
	initModals();
	
	// Initialize tabs
	initTabs();
	
	// Initialize alerts
	initAlerts();
});

/**
 * Initialize tooltips
 */
function initTooltips() {
	const tooltips = document.querySelectorAll('[data-tooltip]');
	
	tooltips.forEach(tooltip => {
		tooltip.addEventListener('mouseenter', function() {
			const tooltipText = this.getAttribute('data-tooltip');
			const tooltipEl = document.createElement('div');
			
			tooltipEl.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-800 rounded shadow-lg';
			tooltipEl.textContent = tooltipText;
			tooltipEl.style.bottom = '100%';
			tooltipEl.style.left = '50%';
			tooltipEl.style.transform = 'translateX(-50%) translateY(-5px)';
			tooltipEl.style.whiteSpace = 'nowrap';
			
			this.style.position = 'relative';
			this.appendChild(tooltipEl);
		});
		
		tooltip.addEventListener('mouseleave', function() {
			const tooltipEl = this.querySelector('div');
			if (tooltipEl) {
				tooltipEl.remove();
			}
		});
	});
}

/**
 * Initialize modals
 */
function initModals() {
	// Modal triggers
	const modalTriggers = document.querySelectorAll('[data-modal-target]');
	
	modalTriggers.forEach(trigger => {
		trigger.addEventListener('click', function(e) {
			e.preventDefault();
			const modalId = this.getAttribute('data-modal-target');
			const modal = document.getElementById(modalId);
			
			if (modal) {
				// Show modal
				modal.classList.remove('hidden');
				document.body.classList.add('overflow-hidden');
				
				// Close button
				const closeBtn = modal.querySelector('[data-modal-close]');
				if (closeBtn) {
					closeBtn.addEventListener('click', function() {
						modal.classList.add('hidden');
						document.body.classList.remove('overflow-hidden');
					});
				}
				
				// Close on backdrop click
				modal.addEventListener('click', function(e) {
					if (e.target === modal) {
						modal.classList.add('hidden');
						document.body.classList.remove('overflow-hidden');
					}
				});
				
				// Close on ESC key
				document.addEventListener('keydown', function(e) {
					if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
						modal.classList.add('hidden');
						document.body.classList.remove('overflow-hidden');
					}
				});
			}
		});
	});
}

/**
 * Initialize tabs
 */
function initTabs() {
	const tabGroups = document.querySelectorAll('[data-tabs]');
	
	tabGroups.forEach(tabGroup => {
		const tabs = tabGroup.querySelectorAll('[data-tab]');
		const tabContents = document.querySelectorAll('[data-tab-content]');
		
		tabs.forEach(tab => {
			tab.addEventListener('click', function() {
				const tabId = this.getAttribute('data-tab');
				
				// Update active tab
				tabs.forEach(t => t.classList.remove('text-blue-600', 'border-blue-600'));
				tab.classList.add('text-blue-600', 'border-blue-600');
				
				// Show active content
				tabContents.forEach(content => {
					if (content.getAttribute('data-tab-content') === tabId) {
						content.classList.remove('hidden');
					} else {
						content.classList.add('hidden');
					}
				});
			});
		});
	});
}

/**
 * Initialize alerts
 */
function initAlerts() {
	const alerts = document.querySelectorAll('.alert');
	
	alerts.forEach(alert => {
		const closeBtn = alert.querySelector('.alert-close');
		
		if (closeBtn) {
			closeBtn.addEventListener('click', function() {
				alert.classList.add('opacity-0');
				setTimeout(() => {
					alert.remove();
				}, 300);
			});
		}
		
		// Auto dismiss after 5 seconds
		if (alert.classList.contains('alert-auto-dismiss')) {
			setTimeout(() => {
				alert.classList.add('opacity-0');
				setTimeout(() => {
					alert.remove();
				}, 300);
			}, 5000);
		}
	});
}

/**
 * Show loading spinner
 */
function showLoading(container) {
	const spinner = document.createElement('div');
	spinner.className = 'loading-spinner flex justify-center items-center p-4';
	spinner.innerHTML = `
		<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
	`;
	
	container.innerHTML = '';
	container.appendChild(spinner);
}

/**
 * Format date for display
 */
function formatDate(dateString) {
	const options = { year: 'numeric', month: 'long', day: 'numeric' };
	return new Date(dateString).toLocaleDateString(undefined, options);
}

/**
 * Format currency for display
 */
function formatCurrency(amount, currency = 'USD') {
	const formatter = new Intl.NumberFormat('en-US', {
		style: 'currency',
		currency: currency,
	});
	
	return formatter.format(amount);
}

/**
 * Confirm action with a dialog
 */
function confirmAction(message, callback) {
	if (confirm(message)) {
		callback();
	}
}