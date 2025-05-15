<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Require login
requireLogin();

// Set page title
$pageTitle = 'Employee List';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Search functionality
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$searchCondition = '';
$params = [];

if (!empty($search)) {
	$searchCondition = "WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR job_position LIKE ?";
	$searchParam = "%$search%";
	$params = [$searchParam, $searchParam, $searchParam, $searchParam];
}

// Get employees
$sql = "SELECT * FROM employees $searchCondition ORDER BY id DESC LIMIT :offset, :limit";
$stmt = $pdo->prepare($sql);

if (!empty($params)) {
	foreach ($params as $index => $param) {
		$stmt->bindValue($index + 1, $param);
	}
}

$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->execute();
$employees = $stmt->fetchAll();

// Count total employees for pagination
$countSql = "SELECT COUNT(*) FROM employees $searchCondition";
$countStmt = $pdo->prepare($countSql);

if (!empty($params)) {
	foreach ($params as $index => $param) {
		$countStmt->bindValue($index + 1, $param);
	}
}

$countStmt->execute();
$totalEmployees = $countStmt->fetchColumn();
$totalPages = ceil($totalEmployees / $perPage);

// Include header
include '../../includes/header.php';

// Include sidebar
include '../../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="ml-64 pt-16 pb-8">
	<div class="p-8">
		<div class="flex justify-between items-center mb-6">
			<h1 class="text-2xl font-semibold text-gray-800">Employee List</h1>
			<a href="add.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-150">
				<i class="fas fa-user-plus mr-2"></i> Add New Employee
			</a>
		</div>
		
		<!-- Search and Filter -->
		<div class="bg-white rounded-lg shadow p-6 mb-6">
			<form action="" method="get" class="flex flex-col md:flex-row gap-4">
				<div class="flex-grow">
					<label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
					<div class="relative">
						<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
							<i class="fas fa-search text-gray-400"></i>
						</div>
						<input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
							class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
							placeholder="Search by name, email or position">
					</div>
				</div>
				<div class="md:self-end">
					<button type="submit" class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-150">
						Search
					</button>
				</div>
				<?php if (!empty($search)): ?>
					<div class="md:self-end">
						<a href="list.php" class="inline-block w-full md:w-auto px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-150 text-center">
							Clear
						</a>
					</div>
				<?php endif; ?>
			</form>
		</div>
		
		<!-- Employee Table -->
		<div class="bg-white rounded-lg shadow">
			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-gray-200">
					<thead class="bg-gray-50">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
						</tr>
					</thead>
					<tbody class="bg-white divide-y divide-gray-200">
						<?php if (!empty($employees)): ?>
							<?php foreach ($employees as $employee): ?>
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
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm text-gray-900">
											<?php echo htmlspecialchars($employee['location']); ?>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm">
										<a href="view.php?id=<?php echo $employee['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3" data-tooltip="View">
											<i class="fas fa-eye"></i>
										</a>
										<a href="edit.php?id=<?php echo $employee['id']; ?>" class="text-green-600 hover:text-green-900 mr-3" data-tooltip="Edit">
											<i class="fas fa-edit"></i>
										</a>
										<a href="#" class="text-red-600 hover:text-red-900" data-tooltip="Delete" 
											onclick="confirmAction('Are you sure you want to delete this employee?', function() { window.location.href = 'delete.php?id=<?php echo $employee['id']; ?>'; }); return false;">
											<i class="fas fa-trash-alt"></i>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="6" class="px-6 py-4 text-center text-gray-500">
									<?php if (!empty($search)): ?>
										No employees found matching "<?php echo htmlspecialchars($search); ?>".
									<?php else: ?>
										No employees found.
									<?php endif; ?>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
			
			<!-- Pagination -->
			<?php if ($totalPages > 1): ?>
				<div class="px-6 py-4 border-t border-gray-200">
					<div class="flex items-center justify-between">
						<div class="text-sm text-gray-700">
							Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to 
							<span class="font-medium"><?php echo min($offset + $perPage, $totalEmployees); ?></span> of 
							<span class="font-medium"><?php echo $totalEmployees; ?></span> results
						</div>
						<div>
							<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
								<?php if ($page > 1): ?>
									<a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
										class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
										<span class="sr-only">Previous</span>
										<i class="fas fa-chevron-left text-xs"></i>
									</a>
								<?php else: ?>
									<span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
										<span class="sr-only">Previous</span>
										<i class="fas fa-chevron-left text-xs"></i>
									</span>
								<?php endif; ?>
								
								<?php
								// Display page numbers
								$startPage = max(1, $page - 2);
								$endPage = min($totalPages, $page + 2);
								
								for ($i = $startPage; $i <= $endPage; $i++):
									$isActive = $i === $page;
									$activeClass = $isActive ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50';
								?>
									<a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
										class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?php echo $activeClass; ?>">
										<?php echo $i; ?>
									</a>
								<?php endfor; ?>
								
								<?php if ($page < $totalPages): ?>
									<a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
										class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
										<span class="sr-only">Next</span>
										<i class="fas fa-chevron-right text-xs"></i>
									</a>
								<?php else: ?>
									<span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
										<span class="sr-only">Next</span>
										<i class="fas fa-chevron-right text-xs"></i>
									</span>
								<?php endif; ?>
							</nav>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
// Set extra JS files
$extraJs = ['/ez-ad-system/assets/js/employee-list.js'];

// Include footer
include '../../includes/footer.php';
?>