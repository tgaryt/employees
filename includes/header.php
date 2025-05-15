<?php
require_once 'auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>EZ-AD | <?php echo $pageTitle ?? 'Employee Management System'; ?></title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gray-100 min-h-screen">
	<!-- Top Navigation Bar -->
	<header class="bg-white shadow fixed top-0 left-64 right-0 z-10">
		<div class="flex justify-between items-center px-6 py-3">
			<div class="flex items-center">
				<button id="sidebar-toggle" class="text-gray-500 focus:outline-none lg:hidden">
					<i class="fas fa-bars"></i>
				</button>
				<h1 class="text-xl font-semibold text-gray-700 ml-2 lg:ml-0"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
			</div>
			
			<div class="flex items-center">
				<div class="relative mr-4">
					<input type="text" placeholder="Search..." class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
					<i class="fas fa-search absolute right-3 top-2.5 text-gray-400"></i>
				</div>
				
				<div class="relative">
					<button class="flex items-center focus:outline-none" id="user-menu-button">
						<div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white">
							<i class="fas fa-user"></i>
						</div>
						<span class="ml-2 mr-1 text-sm font-medium text-gray-700 hidden md:block">Admin</span>
						<i class="fas fa-chevron-down text-xs text-gray-500"></i>
					</button>
					
					<!-- User Dropdown Menu -->
					<div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
						<a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
							<i class="fas fa-user-cog mr-2"></i> Profile
						</a>
						<a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
							<i class="fas fa-cog mr-2"></i> Settings
						</a>
						<div class="border-t border-gray-100"></div>
						<a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
							<i class="fas fa-sign-out-alt mr-2"></i> Logout
						</a>
					</div>
				</div>
			</div>
		</div>
	</header>