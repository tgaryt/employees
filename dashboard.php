<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

// Set page title
$pageTitle = 'Dashboard';

// Get dashboard statistics
$stats = getDashboardStats($pdo);

// Get recent employees (top 5)
$recentEmployees = getAllEmployees($pdo, 5, 0);

// Include header
include 'includes/header.php';

// Include sidebar
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="ml-64 pt-16 pb-8">
	<div class="p-8">
		<!-- Stats Cards -->
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
			<!-- Total Employees -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center">
					<div class="p-3 rounded-full bg-blue-100 text-blue-600">
						<i class="fas fa-users text-xl"></i>
					</div>
					<div class="ml-4">
						<p class="text-sm text-gray-500 font-medium">Total Employees</p>
						<p class="text-2xl font-semibold text-gray-800"><?php echo $stats['total_employees']; ?></p>
					</div>
				</div>
			</div>
			
			<!-- New Hires -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center">
					<div class="p-3 rounded-full bg-green-100 text-green-600">
						<i class="fas fa-user-plus text-xl"></i>
					</div>
					<div class="ml-4">
						<p class="text-sm text-gray-500 font-medium">New Hires (30 days)</p>
						<p class="text-2xl font-semibold text-gray-800"><?php echo $stats['new_employees']; ?></p>
					</div>
				</div>
			</div>
			
			<!-- Pending Documents -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center">
					<div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
						<i class="fas fa-file-alt text-xl"></i>
					</div>
					<div class="ml-4">
						<p class="text-sm text-gray-500 font-medium">Pending Documents</p>
						<p class="text-2xl font-semibold text-gray-800"><?php echo $stats['pending_documents']; ?></p>
					</div>
				</div>
			</div>
			
			<!-- Monthly Payroll -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center">
					<div class="p-3 rounded-full bg-purple-100 text-purple-600">
						<i class="fas fa-money-bill-wave text-xl"></i>
					</div>
					<div class="ml-4">
						<p class="text-sm text-gray-500 font-medium">Monthly Payroll</p>
						<p class="text-2xl font-semibold text-gray-800"><?php echo formatCurrency($stats['monthly_payroll']); ?></p>
					</div>
				</div>
			</div>
		</div>

		<!-- Quick Actions -->
		<div class="bg-white rounded-lg shadow mb-8">
			<div class="px-6 py-4 border-b border-gray-200">
				<h2 class="font-semibold text-lg text-gray-700">Quick Actions</h2>
			</div>
			<div class="p-6 flex flex-wrap gap-4">
				<a href="pages/employees/add.php" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150">
					<i class="fas fa-user-plus mr-2"></i>
					Add Employee
				</a>
				<a href="pages/employees/list.php" class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-150">
					<i class="fas fa-users mr-2"></i>
					View Employees
				</a>
				<a href="pages/reports.php" class="flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-150">
					<i class="fas fa-chart-pie mr-2"></i>
					Generate Report
				</a>
			</div>
		</div>

		<!-- Recent Employees -->
		<div class="bg-white rounded-lg shadow">
			<div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
				<h2 class="font-semibold text-lg text-gray-700">Recent Employees</h2>
				<a href="pages/employees/list.php" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
			</div>
			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-gray-200">
					<thead class="bg-gray-50">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
						</tr>
					</thead>
					<tbody class="bg-white divide-y divide-gray-200">
						<?php if (!empty($recentEmployees)): ?>
							<?php foreach ($recentEmployees as $employee): ?>
								<tr>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="flex items-center">
											<div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-700">
												<i class="fas fa-user"></i>
											</div>
											<div class="ml-4">
												<div class="text-sm font-medium text-gray-900">
													<?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?>
												</div>
												<div class="text-sm text-gray-500">
													<?php echo htmlspecialchars($employee['email']); ?>
												</div>
											</div>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm text-gray-900">
											<?php echo htmlspecialchars($employee['job_position']); ?>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm text-gray-900">
											<?php echo formatDate($employee['start_date']); ?>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm text-gray-900">
											<?php echo formatCurrency($employee['salary'], $employee['currency']); ?>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm">
										<a href="pages/employees/view.php?id=<?php echo $employee['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3" data-tooltip="View">
											<i class="fas fa-eye"></i>
										</a>
										<a href="pages/employees/edit.php?id=<?php echo $employee['id']; ?>" class="text-green-600 hover:text-green-900 mr-3" data-tooltip="Edit">
											<i class="fas fa-edit"></i>
										</a>
										<a href="#" class="text-red-600 hover:text-red-900" data-tooltip="Delete" 
											onclick="confirmAction('Are you sure you want to delete this employee?', function() { window.location.href = 'pages/employees/delete.php?id=<?php echo $employee['id']; ?>'; }); return false;">
											<i class="fas fa-trash-alt"></i>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="5" class="px-6 py-4 text-center text-gray-500">No employees found.</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>