document.addEventListener('DOMContentLoaded', function() {
	// Initialize delete confirmation
	initDeleteConfirmation();
	
	// Initialize search functionality
	initSearch();
});

/**
 * Initialize delete confirmation
 */
function initDeleteConfirmation() {
	const deleteButtons = document.querySelectorAll('[data-tooltip="Delete"]');
	
	deleteButtons.forEach(button => {
		button.addEventListener('click', function(e) {
			e.preventDefault();
			
			const employeeId = this.getAttribute('data-id');
			const employeeName = this.getAttribute('data-name');
			
			if (confirm(`Are you sure you want to delete ${employeeName}? This action cannot be undone.`)) {
				window.location.href = `delete.php?id=${employeeId}`;
			}
		});
	});
}

/**
 * Initialize search functionality
 */
function initSearch() {
	const searchForm = document.querySelector('form');
	const searchInput = document.getElementById('search');
	
	// Submit search on enter key
	searchInput.addEventListener('keyup', function(e) {
		if (e.key === 'Enter') {
			searchForm.submit();
		}
	});
	
	// Clear search when user clicks clear button
	const clearButton = document.querySelector('a[href="list.php"]');
	if (clearButton) {
		clearButton.addEventListener('click', function(e) {
			searchInput.value = '';
			searchForm.submit();
		});
	}
}