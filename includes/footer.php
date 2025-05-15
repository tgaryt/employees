	<footer class="bg-white mt-8 py-4 px-6 shadow-inner">
		<div class="container mx-auto">
			<div class="flex flex-col md:flex-row justify-between items-center">
				<div class="text-sm text-gray-600">
					&copy; <?php echo date('Y'); ?> EZ-AD. All rights reserved.
				</div>
				<div class="text-sm text-gray-600 mt-2 md:mt-0">
					<span>Version 1.0.0</span>
				</div>
			</div>
		</div>
	</footer>
	
	<!-- JavaScript -->
	<script src="/ez-ad-system/assets/js/main.js"></script>
	<?php if (isset($extraJs)): ?>
		<?php foreach ($extraJs as $js): ?>
			<script src="<?php echo $js; ?>"></script>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<script>
		// Toggle user menu
		document.getElementById('user-menu-button').addEventListener('click', function() {
			document.getElementById('user-menu').classList.toggle('hidden');
		});
		
		// Close user menu when clicking outside
		document.addEventListener('click', function(event) {
			const userMenu = document.getElementById('user-menu');
			const userMenuButton = document.getElementById('user-menu-button');
			
			if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
				userMenu.classList.add('hidden');
			}
		});
		
		// Toggle sidebar on mobile
		document.getElementById('sidebar-toggle').addEventListener('click', function() {
			document.getElementById('sidebar').classList.toggle('-translate-x-full');
		});
	</script>
</body>
</html>