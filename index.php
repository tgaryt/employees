<?php
require_once 'includes/auth.php';

// Check if user is logged in
if (isLoggedIn()) {
	// Redirect to dashboard
	header("Location: dashboard.php");
	exit;
} else {
	// Redirect to login page
	header("Location: login.php");
	exit;
}
?>