<?php
session_start();

// Function to check if user is logged in
function isLoggedIn() {
	return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Function to require login
function requireLogin() {
	if (!isLoggedIn()) {
		header("Location: /ez-ad-system/login.php");
		exit;
	}
}

// Function to authenticate user
function authenticateUser($username, $password) {
	// In a real application, this would check against the database
	// For now, we'll use hard-coded credentials for the admin
	$admin_username = 'admin';
	$admin_password_hash = password_hash('admin123', PASSWORD_DEFAULT);
	
	if ($username === $admin_username && password_verify($password, $admin_password_hash)) {
		$_SESSION['admin_logged_in'] = true;
		$_SESSION['admin_username'] = $username;
		$_SESSION['last_activity'] = time();
		return true;
	}
	
	return false;
}

// Function to logout user
function logoutUser() {
	// Unset all session variables
	$_SESSION = array();
	
	// Destroy the session
	session_destroy();
	
	// Redirect to login page
	header("Location: /ez-ad-system/login.php");
	exit;
}

// Function to generate CSRF token
function generateCSRFToken() {
	if (!isset($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf_token'];
}

// Function to verify CSRF token
function verifyCSRFToken($token) {
	if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
		die("CSRF token verification failed");
	}
	return true;
}

// Check session timeout (30 minutes)
function checkSessionTimeout() {
	$timeout = 1800; // 30 minutes in seconds
	
	if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
		logoutUser();
	}
	
	$_SESSION['last_activity'] = time();
}

// If user is logged in, check for session timeout
if (isLoggedIn()) {
	checkSessionTimeout();
}
?>