<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';


// Check if already logged in
if (isLoggedIn()) {
	header("Location: dashboard.php");
	exit;
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Verify CSRF token
	if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
		$error = "Invalid form submission.";
	} else {
		$username = isset($_POST['username']) ? sanitizeInput($_POST['username']) : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		
		if (empty($username) || empty($password)) {
			$error = "Please enter both username and password.";
		} else {
			if (authenticateUser($username, $password)) {
				header("Location: dashboard.php");
				exit;
			} else {
				$error = "Invalid username or password.";
			}
		}
	}
}

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>EZ-AD | Login</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
	<div class="max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden">
		<div class="bg-blue-600 py-6">
			<div class="flex justify-center">
				<h1 class="text-white text-3xl font-bold tracking-wide">EZ-AD</h1>
			</div>
			<div class="flex justify-center mt-1">
				<h2 class="text-blue-100 text-sm">Employee Management System</h2>
			</div>
		</div>
		
		<div class="p-8">
			<h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Admin Login</h2>
			
			<?php if ($error): ?>
				<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
					<p><?php echo $error; ?></p>
				</div>
			<?php endif; ?>
			
			<form action="login.php" method="post">
				<input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
				
				<div class="mb-5">
					<label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username</label>
					<div class="relative">
						<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
							<i class="fas fa-user text-gray-400"></i>
						</div>
						<input type="text" id="username" name="username" 
							class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
							placeholder="Enter your username" required>
					</div>
				</div>
				
				<div class="mb-6">
					<label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
					<div class="relative">
						<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
							<i class="fas fa-lock text-gray-400"></i>
						</div>
						<input type="password" id="password" name="password" 
							class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
							placeholder="Enter your password" required>
					</div>
				</div>
				
				<div class="flex items-center justify-between mb-6">
					<div class="flex items-center">
						<input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
						<label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
					</div>
				</div>
				
				<button type="submit" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm transition duration-150 ease-in-out">
					Sign In
				</button>
			</form>
			
			<div class="text-center mt-6">
				<p class="text-xs text-gray-500">&copy; 2025 EZ-AD. All rights reserved.</p>
			</div>
		</div>
	</div>
	
	<script src="assets/js/login.js"></script>
</body>
</html>