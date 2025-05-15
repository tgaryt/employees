<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Require login
requireLogin();

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	header("Location: list.php");
	exit;
}

$employeeId = (int)$_GET['id'];

// Get employee data
$employee = getEmployeeById($pdo, $employeeId);

// If employee not found, redirect to list
if (!$employee) {
	header("Location: list.php?error=not_found");
	exit;
}

// Process deletion
try {
	// Start transaction
	$pdo->beginTransaction();
	
	// Get all documents for the employee
	$documents = getEmployeeDocuments($pdo, $employeeId);
	
	// Delete all documents from file system
	foreach ($documents as $document) {
		if (file_exists($document['file_path']) && is_file($document['file_path'])) {
			unlink($document['file_path']);
		}
	}
	
	// Delete all documents from database (will be cascaded by foreign key, but for safety)
	$stmt = $pdo->prepare("DELETE FROM employee_documents WHERE employee_id = ?");
	$stmt->execute([$employeeId]);
	
	// Delete employee
	$stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
	$stmt->execute([$employeeId]);
	
	// Commit transaction
	$pdo->commit();
	
	// Redirect to list with success message
	header("Location: list.php?success=deleted&name=" . urlencode($employee['first_name'] . ' ' . $employee['last_name']));
	exit;
	
} catch (PDOException $e) {
	// Rollback transaction
	$pdo->rollBack();
	
	// Redirect to list with error message
	header("Location: list.php?error=delete_failed&message=" . urlencode($e->getMessage()));
	exit;
}
?>