<div id="sidebar" class="fixed inset-y-0 left-0 bg-blue-800 text-white w-64 overflow-y-auto transition-transform duration-300 transform z-20">
	<div class="flex items-center justify-center h-16 border-b border-blue-700">
		<h1 class="text-2xl font-bold">EZ-AD</h1>
	</div>
	<nav class="mt-5">
		<?php
		// Determine current page to highlight active link
		$currentPage = basename($_SERVER['PHP_SELF']);
		$currentDir = basename(dirname($_SERVER['PHP_SELF']));
		
		// Define navigation items
		$navItems = [
			[
				'url' => '/ez-ad-system/dashboard.php',
				'icon' => 'fas fa-tachometer-alt',
				'text' => 'Dashboard',
				'active' => $currentPage === 'dashboard.php'
			],
			[
				'url' => '/ez-ad-system/pages/employees/list.php',
				'icon' => 'fas fa-users',
				'text' => 'Employees',
				'active' => $currentDir === 'employees'
			],
			[
				'url' => '/ez-ad-system/pages/reports.php',
				'icon' => 'fas fa-chart-bar',
				'text' => 'Reports',
				'active' => $currentPage === 'reports.php'
			],
			[
				'url' => '/ez-ad-system/pages/settings.php',
				'icon' => 'fas fa-cog',
				'text' => 'Settings',
				'active' => $currentPage === 'settings.php'
			]
		];
		
		// Generate navigation links
		foreach ($navItems as $item) {
			$activeClass = $item['active'] ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white';
			echo "<a href=\"{$item['url']}\" class=\"flex items-center py-3 px-6 {$activeClass} transition duration-150\">";
			echo "<i class=\"{$item['icon']} mr-3\"></i>";
			echo $item['text'];
			echo "</a>";
		}
		?>
	</nav>
	<div class="absolute bottom-0 w-full border-t border-blue-700">
		<a href="/ez-ad-system/logout.php" class="flex items-center py-3 px-6 text-blue-100 hover:bg-blue-700 hover:text-white transition duration-150">
			<i class="fas fa-sign-out-alt mr-3"></i>
			Logout
		</a>
	</div>
</div>