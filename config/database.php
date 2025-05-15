<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'tgaryt';
$db_user = 'tgaryt';
$db_pass = 'tgaryt';

// Create database connection
try {
	$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
	die("ERROR: Could not connect to the database. " . $e->getMessage());
}
?>