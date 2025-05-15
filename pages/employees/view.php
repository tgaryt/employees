<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Require login
requireLogin();

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	header("Location: list.php");
	exit;
}

$employeeId = (int)$_GET['id'];

// Get employee data
$employee = getEmployeeById($pdo, $employeeId);

// If employee not found, redirect to list
if (!$employee) {
	header("Location: list.php");
	exit;
}

// Get employee documents
$documents = getEmployeeDocuments($pdo, $employeeId);

// Set page title
$pageTitle = htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']);

// Process success message
$successMessage = '';
if (isset($_GET['success'])) {
	if ($_GET['success'] === 'added') {
		$successMessage = "Employee was added successfully.";
	} elseif ($_GET['success'] === 'updated') {
		$successMessage = "Employee information was updated successfully.";
	}
}

// Include header
include '../../includes/header.php';

// Include sidebar
include '../../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="ml-64 pt-16 pb-8">
	<div class="p-8">
		<div class="flex justify-between items-center mb-6">
			<h1 class="text-2xl font-semibold text-gray-800">Employee Details</h1>
			<div class="flex space-x-3">
				<a href="edit.php?id=<?php echo $employeeId; ?>" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition duration-150">
					<i class="fas fa-edit mr-2"></i> Edit
				</a>
				<a href="list.php" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition duration-150">
					<i class="fas fa-arrow-left mr-2"></i> Back to List
				</a>
			</div>
		</div>
		
		<?php if ($successMessage): ?>
			<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 alert alert-auto-dismiss">
				<div class="flex items-center">
					<i class="fas fa-check-circle mr-2"></i>
					<p><?php echo $successMessage; ?></p>
					<button class="ml-auto alert-close">
						<i class="fas fa-times"></i>
					</button>
				</div>
			</div>
		<?php endif; ?>
		
		<!-- Employee Details -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Main Info Card -->
			<div class="lg:col-span-2">
				<div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
					<div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
						<h2 class="font-medium text-gray-700">Employee Information</h2>
						<span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">
							<?php echo htmlspecialchars($employee['job_position']); ?>
						</span>
					</div>
					
					<div class="p-6">
						<div class="flex flex-col md:flex-row md:items-center mb-6">
							<div class="h-20 w-20 bg-gray-300 rounded-full flex items-center justify-center text-gray-700 text-2xl mb-4 md:mb-0 md:mr-6">
								<i class="fas fa-user"></i>
							</div>
							<div>
								<h3 class="text-xl font-semibold">
									<?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?>
								</h3>
								<p class="text-gray-600">
									<i class="fas fa-envelope mr-2"></i> <?php echo htmlspecialchars($employee['email']); ?>
								</p>
								<?php if ($employee['local_cell_number']): ?>
									<p class="text-gray-600">
										<i class="fas fa-phone mr-2"></i> <?php echo htmlspecialchars($employee['local_cell_number']); ?>
									</p>
								<?php endif; ?>
							</div>
						</div>
						
						<div class="border-t border-gray-200 pt-4">
							<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
								<div>
									<h4 class="text-sm font-medium text-gray-500">Job Position</h4>
									<p class="text-gray-800"><?php echo htmlspecialchars($employee['job_position']); ?></p>
								</div>
								
								<div>
									<h4 class="text-sm font-medium text-gray-500">Salary</h4>
									<p class="text-gray-800"><?php echo formatCurrency($employee['salary'], $employee['currency']) . ' per month'; ?></p>
								</div>
								
								<div>
									<h4 class="text-sm font-medium text-gray-500">Employment Type</h4>
									<p class="text-gray-800"><?php echo htmlspecialchars($employee['employment_type']); ?></p>
								</div>
								
								<div>
									<h4 class="text-sm font-medium text-gray-500">Start Date</h4>
									<p class="text-gray-800"><?php echo formatDate($employee['start_date']); ?></p>
								</div>
								
								<div>
									<h4 class="text-sm font-medium text-gray-500">Location</h4>
									<p class="text-gray-800"><?php echo htmlspecialchars($employee['location']); ?></p>
								</div>
								
								<?php if ($employee['passport_number']): ?>
									<div>
										<h4 class="text-sm font-medium text-gray-500">Passport Number</h4>
										<p class="text-gray-800"><?php echo htmlspecialchars($employee['passport_number']); ?></p>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Work Details -->
				<div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
					<div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
						<h2 class="font-medium text-gray-700">Work Details</h2>
					</div>
					
					<div class="p-6">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
							<div>
								<h4 class="text-sm font-medium text-gray-500">Work Hours</h4>
								<p class="text-gray-800"><?php echo htmlspecialchars($employee['work_hours_per_day']) . ' hours/day'; ?></p>
							</div>
							
							<?php if ($employee['work_schedule']): ?>
								<div>
									<h4 class="text-sm font-medium text-gray-500">Work Schedule</h4>
									<p class="text-gray-800"><?php echo htmlspecialchars($employee['work_schedule']); ?></p>
								</div>
							<?php endif; ?>
							
							<?php if ($employee['pst_work_hours']): ?>
								<div>
									<h4 class="text-sm font-medium text-gray-500">PST Work Hours</h4>
									<p class="text-gray-800"><?php echo htmlspecialchars($employee['pst_work_hours']); ?></p>
								</div>
							<?php endif; ?>
							
							<?php if ($employee['payment_method']): ?>
								<div>
									<h4 class="text-sm font-medium text-gray-500">Payment Method</h4>
									<p class="text-gray-800"><?php echo htmlspecialchars($employee['payment_method']); ?></p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				
				<?php if ($employee['residential_address'] || $employee['emergency_contact']): ?>
					<!-- Contact Information -->
					<div class="bg-white rounded-lg shadow-lg overflow-hidden">
						<div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
							<h2 class="font-medium text-gray-700">Contact Information</h2>
						</div>
						
						<div class="p-6">
							<div class="grid grid-cols-1 gap-4">
								<?php if ($employee['residential_address']): ?>
									<div>
										<h4 class="text-sm font-medium text-gray-500">Residential Address</h4>
										<p class="text-gray-800"><?php echo nl2br(htmlspecialchars($employee['residential_address'])); ?></p>
									</div>
								<?php endif; ?>
								
								<?php if ($employee['emergency_contact']): ?>
									<div>
										<h4 class="text-sm font-medium text-gray-500">Emergency Contact</h4>
										<p class="text-gray-800"><?php echo htmlspecialchars($employee['emergency_contact']); ?></p>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
			
			<!-- Sidebar -->
			<div>
				<!-- Documents Card -->
				<div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
					<div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
						<h2 class="font-medium text-gray-700">Documents</h2>
					</div>
					
					<div class="p-6">
						<?php if (!empty($documents)): ?>
							<ul class="divide-y divide-gray-200">
								<?php foreach ($documents as $document): ?>
									<li class="py-3 first:pt-0 last:pb-0">
										<div class="flex items-center">
											<div class="flex-shrink-0 mr-3">
												<?php if ($document['document_type'] === 'offer_letter'): ?>
													<i class="fas fa-file-pdf text-red-500 text-xl"></i>
												<?php elseif ($document['document_type'] === 'id_front'): ?>
													<i class="fas fa-id-card text-blue-500 text-xl"></i>
												<?php elseif ($document['document_type'] === 'id_back'): ?>
													<i class="fas fa-id-card text-green-500 text-xl"></i>
												<?php else: ?>
													<i class="fas fa-file text-gray-500 text-xl"></i>
												<?php endif; ?>
											</div>
											<div class="flex-grow">
												<h4 class="text-sm font-medium text-gray-700">
													<?php 
														if ($document['document_type'] === 'offer_letter') {
															echo "Offer Letter";
														} elseif ($document['document_type'] === 'id_front') {
															echo "ID Front";
														} elseif ($document['document_type'] === 'id_back') {
															echo "ID Back";
														} else {
															echo htmlspecialchars($document['document_type']);
														}
													?>
												</h4>
												<p class="text-xs text-gray-500"><?php echo htmlspecialchars($document['file_name']); ?></p>
											</div>
											<div>
												<a href="<?php echo htmlspecialchars($document['file_path']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
													<i class="fas fa-eye"></i>
												</a>
											</div>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php else: ?>
							<p class="text-gray-500 text-sm">No documents uploaded yet.</p>
						<?php endif; ?>
						
						<div class="mt-4 pt-4 border-t border-gray-200">
							<a href="edit.php?id=<?php echo $employeeId; ?>#documents" class="flex items-center text-sm text-blue-600 hover:text-blue-800">
								<i class="fas fa-upload mr-2"></i> Upload Documents
							</a>
						</div>
					</div>
				</div>
				
				<!-- Additional Information -->
				<div class="bg-white rounded-lg shadow-lg overflow-hidden">
					<div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
						<h2 class="font-medium text-gray-700">Additional Information</h2>
					</div>
					
					<div class="p-6">
						<div class="grid grid-cols-1 gap-4">
							<div>
								<h4 class="text-sm font-medium text-gray-500">Created At</h4>
								<p class="text-gray-800"><?php echo formatDate($employee['created_at'], 'M d, Y h:i A'); ?></p>
							</div>
							
							<div>
								<h4 class="text-sm font-medium text-gray-500">Last Updated</h4>
								<p class="text-gray-800"><?php echo formatDate($employee['updated_at'], 'M d, Y h:i A'); ?></p>
							</div>
						</div>
						
						<div class="mt-4 pt-4 border-t border-gray-200 flex space-x-3">
							<a href="edit.php?id=<?php echo $employeeId; ?>" class="text-sm text-green-600 hover:text-green-800">
								<i class="fas fa-edit mr-1"></i> Edit
							</a>
							<a href="#" class="text-sm text-red-600 hover:text-red-800" 
							   onclick="confirmAction('Are you sure you want to delete this employee? This action cannot be undone.', function() { window.location.href = 'delete.php?id=<?php echo $employeeId; ?>'; }); return false;">
								<i class="fas fa-trash-alt mr-1"></i> Delete
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
// Include footer
include '../../includes/footer.php';
?>