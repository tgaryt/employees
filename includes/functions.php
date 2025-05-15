<?php
// Function to sanitize user input
function sanitizeInput($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

// Function to validate email
function validateEmail($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate date
function validateDate($date, $format = 'Y-m-d') {
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) === $date;
}

// Function to format currency
function formatCurrency($amount, $currency = 'USD') {
	$currencies = [
		'USD' => '$',
		'EUR' => '€',
		'GBP' => '£',
		'JOD' => 'JD'
	];
	
	$symbol = isset($currencies[$currency]) ? $currencies[$currency] : '';
	return $symbol . number_format($amount, 2);
}

// Function to format date for display
function formatDate($date, $format = 'M d, Y') {
	return date($format, strtotime($date));
}

// Function to get employee by ID
function getEmployeeById($pdo, $id) {
	$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
	$stmt->execute([$id]);
	return $stmt->fetch();
}

// Function to get all employees
function getAllEmployees($pdo, $limit = null, $offset = null) {
	$sql = "SELECT * FROM employees ORDER BY id DESC";
	
	if ($limit !== null && $offset !== null) {
		$sql .= " LIMIT :offset, :limit";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
	} else {
		$stmt = $pdo->prepare($sql);
	}
	
	$stmt->execute();
	return $stmt->fetchAll();
}

// Function to count total employees
function countEmployees($pdo) {
	$stmt = $pdo->query("SELECT COUNT(*) FROM employees");
	return $stmt->fetchColumn();
}

// Function to count new employees (last 30 days)
function countNewEmployees($pdo) {
	$stmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
	$stmt->execute();
	return $stmt->fetchColumn();
}

// Function to get employee documents
function getEmployeeDocuments($pdo, $employeeId) {
	$stmt = $pdo->prepare("SELECT * FROM employee_documents WHERE employee_id = ?");
	$stmt->execute([$employeeId]);
	return $stmt->fetchAll();
}

// Function to check if document exists
function documentExists($pdo, $employeeId, $documentType) {
	$stmt = $pdo->prepare("SELECT COUNT(*) FROM employee_documents WHERE employee_id = ? AND document_type = ?");
	$stmt->execute([$employeeId, $documentType]);
	return $stmt->fetchColumn() > 0;
}

// Function to handle file upload
function uploadFile($file, $targetDir, $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png']) {
	// Create target directory if it doesn't exist
	if (!file_exists($targetDir)) {
		mkdir($targetDir, 0755, true);
	}
	
	// Get file extension
	$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
	
	// Check if file type is allowed
	if (!in_array($fileExtension, $allowedTypes)) {
		return [
			'success' => false,
			'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes)
		];
	}
	
	// Generate unique filename
	$newFileName = uniqid() . '.' . $fileExtension;
	$targetFile = $targetDir . $newFileName;
	
	// Try to upload file
	if (move_uploaded_file($file['tmp_name'], $targetFile)) {
		return [
			'success' => true,
			'file_name' => $file['name'],
			'file_path' => $targetFile
		];
	} else {
		return [
			'success' => false,
			'message' => 'Failed to upload file.'
		];
	}
}

// Function to generate dashboard stats
function getDashboardStats($pdo) {
	return [
		'total_employees' => countEmployees($pdo),
		'new_employees' => countNewEmployees($pdo),
		'pending_documents' => getPendingDocumentsCount($pdo),
		'monthly_payroll' => calculateMonthlyPayroll($pdo)
	];
}

// Function to count pending documents
function getPendingDocumentsCount($pdo) {
	$sql = "SELECT COUNT(*) FROM employees WHERE id NOT IN (
		SELECT DISTINCT employee_id FROM employee_documents
		WHERE document_type = 'offer_letter'
	)";
	$stmt = $pdo->query($sql);
	return $stmt->fetchColumn();
}

// Function to calculate monthly payroll
function calculateMonthlyPayroll($pdo) {
	$stmt = $pdo->query("SELECT SUM(salary) FROM employees");
	return $stmt->fetchColumn();
}
?>