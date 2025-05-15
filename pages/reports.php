<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Require login
requireLogin();

// Set page title
$pageTitle = 'Reports';

// Get report type if set
$reportType = isset($_GET['type']) ? sanitizeInput($_GET['type']) : 'employees';

// Get date range if set
$startDate = isset($_GET['start_date']) ? sanitizeInput($_GET['start_date']) : '';
$endDate = isset($_GET['end_date']) ? sanitizeInput($_GET['end_date']) : '';

// Get report data based on type and date range
$reportData = [];
$sql = '';
$params = [];

// Process report generation
if (!empty($reportType)) {
	switch ($reportType) {
		case 'employees':
			// All employees report
			$sql = "SELECT * FROM employees ORDER BY first_name, last_name";
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			$reportData = $stmt->fetchAll();
			break;
			
		case 'new_hires':
			// New hires report with date range
			$sql = "SELECT * FROM employees WHERE 1=1";
			
			if (!empty($startDate) && validateDate($startDate)) {
				$sql .= " AND start_date >= ?";
				$params[] = $startDate;
			}
			
			if (!empty($endDate) && validateDate($endDate)) {
				$sql .= " AND start_date <= ?";
				$params[] = $endDate;
			}
			
			$sql .= " ORDER BY start_date DESC";
			
			$stmt = $pdo->prepare($sql);
			$stmt->execute($params);
			$reportData = $stmt->fetchAll();
			break;
			
		case 'salary':
			// Salary report
			$sql = "SELECT job_position, COUNT(*) as employee_count, AVG(salary) as avg_salary, MIN(salary) as min_salary, MAX(salary) as max_salary, SUM(salary) as total_salary
					FROM employees
					GROUP BY job_position
					ORDER BY avg_salary DESC";
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			$reportData = $stmt->fetchAll();
			break;
			
		case 'documents':
			// Missing documents report
			$sql = "SELECT e.id, e.first_name, e.last_name, e.email, e.job_position,
					CASE WHEN offer_letter.id IS NULL THEN 'Missing' ELSE 'Uploaded' END as offer_letter_status,
					CASE WHEN id_front.id IS NULL THEN 'Missing' ELSE 'Uploaded' END as id_front_status,
					CASE WHEN id_back.id IS NULL THEN 'Missing' ELSE 'Uploaded' END as id_back_status
					FROM employees e
					LEFT JOIN employee_documents offer_letter ON e.id = offer_letter.employee_id AND offer_letter.document_type = 'offer_letter'
					LEFT JOIN employee_documents id_front ON e.id = id_front.employee_id AND id_front.document_type = 'id_front'
					LEFT JOIN employee_documents id_back ON e.id = id_back.employee_id AND id_back.document_type = 'id_back'
					ORDER BY e.first_name, e.last_name";
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			$reportData = $stmt->fetchAll();
			break;
			
		case 'locations':
			// Locations report
			$sql = "SELECT location, COUNT(*) as employee_count
					FROM employees
					GROUP BY location
					ORDER BY employee_count DESC";
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			$reportData = $stmt->fetchAll();
			break;
	}
}

// Generate CSRF token
$csrf_token = generateCSRFToken();

// Include header
include '../includes/header.php';

// Include sidebar
include '../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="ml-64 pt-16 pb-8">
	<div class="p-8">
		<div class="flex justify-between items-center mb-6">
			<h1 class="text-2xl font-semibold text-gray-800">Reports</h1>
			<?php if (!empty($reportType) && !empty($reportData)): ?>
				<button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-150">
					<i class="fas fa-print mr-2"></i> Print Report
				</button>
			<?php endif; ?>
		</div>
		
		<!-- Report Filters -->
		<div class="bg-white rounded-lg shadow p-6 mb-6">
			<form action="reports.php" method="get" class="flex flex-col md:flex-row gap-4">
				<div class="flex-grow">
					<label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
					<select id="report_type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<option value="employees" <?php echo $reportType === 'employees' ? 'selected' : ''; ?>>All Employees</option>
						<option value="new_hires" <?php echo $reportType === 'new_hires' ? 'selected' : ''; ?>>New Hires</option>
						<option value="salary" <?php echo $reportType === 'salary' ? 'selected' : ''; ?>>Salary Analysis</option>
						<option value="documents" <?php echo $reportType === 'documents' ? 'selected' : ''; ?>>Document Status</option>
						<option value="locations" <?php echo $reportType === 'locations' ? 'selected' : ''; ?>>Locations</option>
					</select>
				</div>
				
				<div class="date-range <?php echo $reportType !== 'new_hires' ? 'hidden' : ''; ?>">
					<label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
					<input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" 
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
				</div>
				
				<div class="date-range <?php echo $reportType !== 'new_hires' ? 'hidden' : ''; ?>">
					<label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
					<input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" 
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
				</div>
				
				<div class="md:self-end">
					<button type="submit" class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-150">
						Generate Report
					</button>
				</div>
			</form>
		</div>
		
		<!-- Report Content -->
		<div class="bg-white rounded-lg shadow overflow-hidden">
			<div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
				<h2 class="font-medium text-gray-700">
					<?php 
						switch ($reportType) {
							case 'employees':
								echo 'All Employees Report';
								break;
							case 'new_hires':
								echo 'New Hires Report';
								if (!empty($startDate) || !empty($endDate)) {
									echo ' (';
									if (!empty($startDate)) {
										echo 'From ' . formatDate($startDate);
									}
									if (!empty($startDate) && !empty($endDate)) {
										echo ' - ';
									}
									if (!empty($endDate)) {
										echo 'To ' . formatDate($endDate);
									}
									echo ')';
								}
								break;
							case 'salary':
								echo 'Salary Analysis Report';
								break;
							case 'documents':
								echo 'Document Status Report';
								break;
							case 'locations':
								echo 'Locations Report';
								break;
							default:
								echo 'Report';
						}
					?>
				</h2>
			</div>
			
			<div class="p-6">
				<?php if (empty($reportData)): ?>
					<div class="text-center py-4">
						<p class="text-gray-500">No data available for this report.</p>
						<p class="text-sm text-gray-400 mt-2">Try adjusting your filters or select a different report type.</p>
					</div>
				<?php else: ?>
					<!-- Report Tables -->
					<?php if ($reportType === 'employees' || $reportType === 'new_hires'): ?>
						<!-- Employees or New Hires Report -->
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Position</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
									</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
									<?php foreach ($reportData as $employee): ?>
										<tr>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900">
													<?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?>
												</div>
												<div class="text-sm text-gray-500">
													<?php echo htmlspecialchars($employee['email']); ?>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900">
													<?php echo htmlspecialchars($employee['job_position']); ?>
												</div>
												<div class="text-sm text-gray-500">
													<?php echo htmlspecialchars($employee['employment_type']); ?>
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
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900">
													<?php echo htmlspecialchars($employee['location']); ?>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<?php if ($employee['local_cell_number']): ?>
													<div class="text-sm text-gray-900">
														<?php echo htmlspecialchars($employee['local_cell_number']); ?>
													</div>
												<?php endif; ?>
												<?php if ($employee['emergency_contact']): ?>
													<div class="text-sm text-gray-500">
														Emerg: <?php echo htmlspecialchars($employee['emergency_contact']); ?>
													</div>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						
						<!-- Summary -->
						<div class="mt-6 pt-6 border-t border-gray-200">
							<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
								<div class="bg-gray-50 p-4 rounded-md">
									<p class="text-sm text-gray-500">Total Employees</p>
									<p class="text-xl font-semibold"><?php echo count($reportData); ?></p>
								</div>
								
								<?php if (count($reportData) > 0): ?>
									<div class="bg-gray-50 p-4 rounded-md">
										<p class="text-sm text-gray-500">Average Salary</p>
										<p class="text-xl font-semibold">
											<?php 
												$totalSalary = array_sum(array_column($reportData, 'salary'));
												echo formatCurrency($totalSalary / count($reportData));
											?>
										</p>
									</div>
									
									<div class="bg-gray-50 p-4 rounded-md">
										<p class="text-sm text-gray-500">Monthly Payroll</p>
										<p class="text-xl font-semibold">
											<?php echo formatCurrency($totalSalary); ?>
										</p>
									</div>
									
									<div class="bg-gray-50 p-4 rounded-md">
										<p class="text-sm text-gray-500">Report Generated</p>
										<p class="text-xl font-semibold">
											<?php echo date('M d, Y'); ?>
										</p>
									</div>
								<?php endif; ?>
							</div>
						</div>
						
					<?php elseif ($reportType === 'salary'): ?>
						<!-- Salary Analysis Report -->
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Position</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Salary</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minimum</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maximum</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
									</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
									<?php foreach ($reportData as $row): ?>
										<tr>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900">
													<?php echo htmlspecialchars($row['job_position']); ?>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900">
													<?php echo $row['employee_count']; ?>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900">
													<?php echo formatCurrency(round($row['avg_salary'], 2)); ?>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900">
													<?php echo formatCurrency($row['min_salary']); ?>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900">
													<?php echo formatCurrency($row['max_salary']); ?>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900">
													<?php echo formatCurrency($row['total_salary']); ?>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						
					<?php elseif ($reportType === 'documents'): ?>
						<!-- Document Status Report -->
						<div class="overflow-x-auto">
							<table class="min-w-full divide-y divide-gray-200">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Position</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offer Letter</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Front</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Back</th>
										<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
									</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200">
									<?php foreach ($reportData as $row): ?>
										<tr>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900">
													<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
												</div>
												<div class="text-sm text-gray-500">
													<?php echo htmlspecialchars($row['email']); ?>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900">
													<?php echo htmlspecialchars($row['job_position']); ?>
												</div>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<?php if ($row['offer_letter_status'] === 'Uploaded'): ?>
													<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
														Uploaded
													</span>
												<?php else: ?>
													<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
														Missing
													</span>
												<?php endif; ?>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<?php if ($row['id_front_status'] === 'Uploaded'): ?>
													<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
														Uploaded
													</span>
												<?php else: ?>
													<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
														Missing
													</span>
												<?php endif; ?>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<?php if ($row['id_back_status'] === 'Uploaded'): ?>
													<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
														Uploaded
													</span>
												<?php else: ?>
													<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
														Missing
													</span>
												<?php endif; ?>
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm">
												<a href="../pages/employees/edit.php?id=<?php echo $row['id']; ?>#documents" class="text-blue-600 hover:text-blue-900">
													<i class="fas fa-upload mr-1"></i> Upload
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						
					<?php elseif ($reportType === 'locations'): ?>
						<!-- Locations Report -->
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<div>
								<div class="overflow-x-auto">
									<table class="min-w-full divide-y divide-gray-200">
										<thead class="bg-gray-50">
											<tr>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Count</th>
												<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
											</tr>
										</thead>
										<tbody class="bg-white divide-y divide-gray-200">
											<?php 
												$totalEmployees = array_sum(array_column($reportData, 'employee_count'));
												foreach ($reportData as $row): 
											?>
												<tr>
													<td class="px-6 py-4 whitespace-nowrap">
														<div class="text-sm font-medium text-gray-900">
															<?php echo htmlspecialchars($row['location']); ?>
														</div>
													</td>
													<td class="px-6 py-4 whitespace-nowrap">
														<div class="text-sm text-gray-900">
															<?php echo $row['employee_count']; ?>
														</div>
													</td>
													<td class="px-6 py-4 whitespace-nowrap">
														<div class="text-sm text-gray-900">
															<?php echo round(($row['employee_count'] / $totalEmployees) * 100, 1) . '%'; ?>
														</div>
														<div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
															<div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo ($row['employee_count'] / $totalEmployees) * 100; ?>%"></div>
														</div>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
							
							<div>
								<div class="bg-gray-50 p-6 rounded-lg h-full">
									<h3 class="text-lg font-medium text-gray-700 mb-4">Location Distribution</h3>
									<div class="space-y-4">
										<?php foreach ($reportData as $row): ?>
											<div>
												<div class="flex justify-between text-sm mb-1">
													<span class="font-medium"><?php echo htmlspecialchars($row['location']); ?></span>
													<span><?php echo $row['employee_count']; ?> (<?php echo round(($row['employee_count'] / $totalEmployees) * 100, 1); ?>%)</span>
												</div>
												<div class="w-full bg-gray-200 rounded-full h-3">
													<div class="bg-blue-600 h-3 rounded-full" style="width: <?php echo ($row['employee_count'] / $totalEmployees) * 100; ?>%"></div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
									
									<div class="mt-6 pt-6 border-t border-gray-200">
										<p class="text-sm text-gray-500">Total Employees</p>
										<p class="text-xl font-semibold"><?php echo $totalEmployees; ?></p>
										<p class="text-sm text-gray-500 mt-4">Unique Locations</p>
										<p class="text-xl font-semibold"><?php echo count($reportData); ?></p>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<script>
	// JavaScript for report filters
	document.addEventListener('DOMContentLoaded', function() {
		const reportTypeSelect = document.getElementById('report_type');
		const dateRangeFields = document.querySelectorAll('.date-range');
		
		// Show/hide date range fields based on report type
		reportTypeSelect.addEventListener('change', function() {
			if (this.value === 'new_hires') {
				dateRangeFields.forEach(field => {
					field.classList.remove('hidden');
				});
			} else {
				dateRangeFields.forEach(field => {
					field.classList.add('hidden');
				});
			}
		});
	});
</script>

<?php
// Include footer
include '../includes/footer.php';
?>