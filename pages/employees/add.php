<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Require login
requireLogin();

// Set page title
$pageTitle = 'Add New Employee';

// Define variables and set to empty values
$firstName = $lastName = $email = $jobPosition = $salary = $currency = '';
$location = $residentialAddress = $paymentMethod = $employmentType = '';
$workHours = $workSchedule = $pstWorkHours = $passportNumber = '';
$localCellNumber = $emergencyContact = $startDate = '';

$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Verify CSRF token
	if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
		$errors['form'] = "Invalid form submission.";
	} else {
		// Validate and sanitize input
		$firstName = sanitizeInput($_POST['first_name'] ?? '');
		$lastName = sanitizeInput($_POST['last_name'] ?? '');
		$email = sanitizeInput($_POST['email'] ?? '');
		$jobPosition = sanitizeInput($_POST['job_position'] ?? '');
		$salary = sanitizeInput($_POST['salary'] ?? '');
		$currency = sanitizeInput($_POST['currency'] ?? 'USD');
		$location = sanitizeInput($_POST['location'] ?? '');
		$residentialAddress = sanitizeInput($_POST['residential_address'] ?? '');
		$paymentMethod = sanitizeInput($_POST['payment_method'] ?? '');
		$employmentType = sanitizeInput($_POST['employment_type'] ?? '');
		$workHours = sanitizeInput($_POST['work_hours'] ?? '');
		$workSchedule = sanitizeInput($_POST['work_schedule'] ?? '');
		$pstWorkHours = sanitizeInput($_POST['pst_work_hours'] ?? '');
		$passportNumber = sanitizeInput($_POST['passport_number'] ?? '');
		$localCellNumber = sanitizeInput($_POST['local_cell_number'] ?? '');
		$emergencyContact = sanitizeInput($_POST['emergency_contact'] ?? '');
		$startDate = sanitizeInput($_POST['start_date'] ?? '');
		
		// Validate required fields
		if (empty($firstName)) {
			$errors['first_name'] = "First name is required.";
		}
		
		if (empty($lastName)) {
			$errors['last_name'] = "Last name is required.";
		}
		
		if (empty($email)) {
			$errors['email'] = "Email is required.";
		} elseif (!validateEmail($email)) {
			$errors['email'] = "Invalid email format.";
		}
		
		if (empty($jobPosition)) {
			$errors['job_position'] = "Job position is required.";
		}
		
		if (empty($salary)) {
			$errors['salary'] = "Salary is required.";
		} elseif (!is_numeric($salary)) {
			$errors['salary'] = "Salary must be a number.";
		}
		
		if (empty($location)) {
			$errors['location'] = "Location is required.";
		}
		
		if (empty($employmentType)) {
			$errors['employment_type'] = "Employment type is required.";
		}
		
		if (empty($workHours)) {
			$errors['work_hours'] = "Work hours are required.";
		} elseif (!is_numeric($workHours)) {
			$errors['work_hours'] = "Work hours must be a number.";
		}
		
		if (empty($startDate)) {
			$errors['start_date'] = "Start date is required.";
		} elseif (!validateDate($startDate)) {
			$errors['start_date'] = "Invalid date format. Please use YYYY-MM-DD.";
		}
		
		// If no errors, insert employee into database
		if (empty($errors)) {
			try {
				$stmt = $pdo->prepare("
					INSERT INTO employees (
						first_name, last_name, email, job_position, salary, currency,
						location, residential_address, payment_method, employment_type,
						work_hours_per_day, work_schedule, pst_work_hours, passport_number,
						local_cell_number, emergency_contact, start_date
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				");
				
				$stmt->execute([
					$firstName, $lastName, $email, $jobPosition, $salary, $currency,
					$location, $residentialAddress, $paymentMethod, $employmentType,
					$workHours, $workSchedule, $pstWorkHours, $passportNumber,
					$localCellNumber, $emergencyContact, $startDate
				]);
				
				$employeeId = $pdo->lastInsertId();
				
				// Handle file uploads
				if (!empty($_FILES['offer_letter']['name'])) {
					$uploadResult = uploadFile(
						$_FILES['offer_letter'],
						'../../assets/uploads/offer_letters/',
						['pdf']
					);
					
					if ($uploadResult['success']) {
						$stmt = $pdo->prepare("
							INSERT INTO employee_documents (
								employee_id, document_type, file_name, file_path
							) VALUES (?, ?, ?, ?)
						");
						
						$stmt->execute([
							$employeeId,
							'offer_letter',
							$uploadResult['file_name'],
							$uploadResult['file_path']
						]);
					} else {
						$errors['offer_letter'] = $uploadResult['message'];
					}
				}
				
				if (!empty($_FILES['id_front']['name'])) {
					$uploadResult = uploadFile(
						$_FILES['id_front'],
						'../../assets/uploads/id_front/',
						['jpg', 'jpeg', 'png', 'pdf']
					);
					
					if ($uploadResult['success']) {
						$stmt = $pdo->prepare("
							INSERT INTO employee_documents (
								employee_id, document_type, file_name, file_path
							) VALUES (?, ?, ?, ?)
						");
						
						$stmt->execute([
							$employeeId,
							'id_front',
							$uploadResult['file_name'],
							$uploadResult['file_path']
						]);
					} else {
						$errors['id_front'] = $uploadResult['message'];
					}
				}
				
				if (!empty($_FILES['id_back']['name'])) {
					$uploadResult = uploadFile(
						$_FILES['id_back'],
						'../../assets/uploads/id_back/',
						['jpg', 'jpeg', 'png', 'pdf']
					);
					
					if ($uploadResult['success']) {
						$stmt = $pdo->prepare("
							INSERT INTO employee_documents (
								employee_id, document_type, file_name, file_path
							) VALUES (?, ?, ?, ?)
						");
						
						$stmt->execute([
							$employeeId,
							'id_back',
							$uploadResult['file_name'],
							$uploadResult['file_path']
						]);
					} else {
						$errors['id_back'] = $uploadResult['message'];
					}
				}
				
				if (empty($errors)) {
					// Redirect to employee view page
					header("Location: view.php?id=$employeeId&success=added");
					exit;
				}
				
			} catch (PDOException $e) {
				if ($e->getCode() == 23000) {
					$errors['email'] = "This email is already in use.";
				} else {
					$errors['form'] = "Database error: " . $e->getMessage();
				}
			}
		}
	}
}

// Generate CSRF token
$csrf_token = generateCSRFToken();

// Include header
include '../../includes/header.php';

// Include sidebar
include '../../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="ml-64 pt-16 pb-8">
	<div class="p-8">
		<div class="flex justify-between items-center mb-6">
			<h1 class="text-2xl font-semibold text-gray-800">Add New Employee</h1>
			<a href="list.php" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition duration-150">
				<i class="fas fa-arrow-left mr-2"></i> Back to List
			</a>
		</div>
		
		<?php if (isset($errors['form'])): ?>
			<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
				<p><?php echo $errors['form']; ?></p>
			</div>
		<?php endif; ?>
		
		<!-- Employee Form -->
		<div class="bg-white rounded-lg shadow-lg overflow-hidden">
			<div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
				<h2 class="font-medium text-gray-700">Employee Information</h2>
			</div>
			
			<form action="add.php" method="post" enctype="multipart/form-data" class="p-6">
				<input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
				
				<!-- Form Tabs -->
				<div class="mb-6 border-b border-gray-200" data-tabs>
					<ul class="flex flex-wrap -mb-px">
						<li class="mr-2">
							<a href="#" class="inline-block py-2 px-4 text-blue-600 border-b-2 border-blue-600 font-medium" data-tab="basic-info">
								Basic Info
							</a>
						</li>
						<li class="mr-2">
							<a href="#" class="inline-block py-2 px-4 text-gray-500 hover:text-gray-700 border-b-2 border-transparent font-medium" data-tab="employment">
								Employment Details
							</a>
						</li>
						<li class="mr-2">
							<a href="#" class="inline-block py-2 px-4 text-gray-500 hover:text-gray-700 border-b-2 border-transparent font-medium" data-tab="contact">
								Contact Info
							</a>
						</li>
						<li class="mr-2">
							<a href="#" class="inline-block py-2 px-4 text-gray-500 hover:text-gray-700 border-b-2 border-transparent font-medium" data-tab="documents">
								Documents
							</a>
						</li>
					</ul>
				</div>
				
				<!-- Basic Info Tab -->
				<div data-tab-content="basic-info">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
						<div>
							<label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
							<input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>" 
								class="w-full px-3 py-2 border <?php echo isset($errors['first_name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
								required>
							<?php if (isset($errors['first_name'])): ?>
								<p class="text-red-500 text-xs mt-1"><?php echo $errors['first_name']; ?></p>
							<?php endif; ?>
						</div>
						
						<div>
							<label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
							<input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($lastName); ?>" 
								class="w-full px-3 py-2 border <?php echo isset($errors['last_name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
								required>
							<?php if (isset($errors['last_name'])): ?>
								<p class="text-red-500 text-xs mt-1"><?php echo $errors['last_name']; ?></p>
							<?php endif; ?>
						</div>
					</div>
					
					<div class="mb-6">
						<label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
						<input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
							class="w-full px-3 py-2 border <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
							required>
						<?php if (isset($errors['email'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['email']; ?></p>
						<?php endif; ?>
					</div>
					
					<div class="mb-6">
						<label for="passport_number" class="block text-sm font-medium text-gray-700 mb-1">Passport Number</label>
						<input type="text" id="passport_number" name="passport_number" value="<?php echo htmlspecialchars($passportNumber); ?>" 
							class="w-full px-3 py-2 border <?php echo isset($errors['passport_number']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<?php if (isset($errors['passport_number'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['passport_number']; ?></p>
						<?php endif; ?>
					</div>
				</div>
				
				<!-- Employment Details Tab -->
				<div data-tab-content="employment" class="hidden">
					<div class="mb-6">
						<label for="job_position" class="block text-sm font-medium text-gray-700 mb-1">Job Position *</label>
						<input type="text" id="job_position" name="job_position" value="<?php echo htmlspecialchars($jobPosition); ?>" 
							class="w-full px-3 py-2 border <?php echo isset($errors['job_position']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
							required>
						<?php if (isset($errors['job_position'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['job_position']; ?></p>
						<?php endif; ?>
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
						<div>
							<label for="salary" class="block text-sm font-medium text-gray-700 mb-1">Salary *</label>
							<input type="number" id="salary" name="salary" value="<?php echo htmlspecialchars($salary); ?>" step="0.01" min="0" 
								class="w-full px-3 py-2 border <?php echo isset($errors['salary']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
								required>
							<?php if (isset($errors['salary'])): ?>
								<p class="text-red-500 text-xs mt-1"><?php echo $errors['salary']; ?></p>
							<?php endif; ?>
						</div>
						
						<div>
							<label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
							<select id="currency" name="currency" 
								class="w-full px-3 py-2 border <?php echo isset($errors['currency']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
								required>
								<option value="USD" <?php echo $currency === 'USD' ? 'selected' : ''; ?>>USD</option>
								<option value="EUR" <?php echo $currency === 'EUR' ? 'selected' : ''; ?>>EUR</option>
								<option value="GBP" <?php echo $currency === 'GBP' ? 'selected' : ''; ?>>GBP</option>
								<option value="JOD" <?php echo $currency === 'JOD' ? 'selected' : ''; ?>>JOD</option>
							</select>
							<?php if (isset($errors['currency'])): ?>
								<p class="text-red-500 text-xs mt-1"><?php echo $errors['currency']; ?></p>
							<?php endif; ?>
						</div>
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
						<div>
							<label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
							<input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" 
								class="w-full px-3 py-2 border <?php echo isset($errors['location']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
								required>
							<?php if (isset($errors['location'])): ?>
								<p class="text-red-500 text-xs mt-1"><?php echo $errors['location']; ?></p>
							<?php endif; ?>
						</div>
						
						<div>
							<label for="employment_type" class="block text-sm font-medium text-gray-700 mb-1">Employment Type *</label>
							<select id="employment_type" name="employment_type" 
								class="w-full px-3 py-2 border <?php echo isset($errors['employment_type']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
								required>
								<option value="">Select Type</option>
								<option value="Full-time" <?php echo $employmentType === 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
								<option value="Part-time" <?php echo $employmentType === 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
								<option value="Contract" <?php echo $employmentType === 'Contract' ? 'selected' : ''; ?>>Contract</option>
								<option value="Freelance" <?php echo $employmentType === 'Freelance' ? 'selected' : ''; ?>>Freelance</option>
								<option value="Monthly" <?php echo $employmentType === 'Monthly' ? 'selected' : ''; ?>>Monthly</option>
							</select>
							<?php if (isset($errors['employment_type'])): ?>
								<p class="text-red-500 text-xs mt-1"><?php echo $errors['employment_type']; ?></p>
							<?php endif; ?>
						</div>
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
						<div>
							<label for="work_hours" class="block text-sm font-medium text-gray-700 mb-1">Work Hours (per day) *</label>
							<input type="number" id="work_hours" name="work_hours" value="<?php echo htmlspecialchars($workHours); ?>" min="1" max="24" 
								class="w-full px-3 py-2 border <?php echo isset($errors['work_hours']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
								required>
							<?php if (isset($errors['work_hours'])): ?>
								<p class="text-red-500 text-xs mt-1"><?php echo $errors['work_hours']; ?></p>
							<?php endif; ?>
						</div>
						
						<div>
							<label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
							<input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" 
								class="w-full px-3 py-2 border <?php echo isset($errors['start_date']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
								required>
							<?php if (isset($errors['start_date'])): ?>
								<p class="text-red-500 text-xs mt-1"><?php echo $errors['start_date']; ?></p>
							<?php endif; ?>
						</div>
					</div>
					
					<div class="mb-6">
						<label for="work_schedule" class="block text-sm font-medium text-gray-700 mb-1">Work Schedule</label>
						<input type="text" id="work_schedule" name="work_schedule" value="<?php echo htmlspecialchars($workSchedule); ?>" 
							class="w-full px-3 py-2 border <?php echo isset($errors['work_schedule']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
							placeholder="e.g. Monday through Friday">
						<?php if (isset($errors['work_schedule'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['work_schedule']; ?></p>
						<?php endif; ?>
					</div>
					
					<div class="mb-6">
						<label for="pst_work_hours" class="block text-sm font-medium text-gray-700 mb-1">PST Work Hours</label>
						<input type="text" id="pst_work_hours" name="pst_work_hours" value="<?php echo htmlspecialchars($pstWorkHours); ?>" 
							class="w-full px-3 py-2 border <?php echo isset($errors['pst_work_hours']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
							placeholder="e.g. 06:00 AM - 3:00 PM PST">
						<?php if (isset($errors['pst_work_hours'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['pst_work_hours']; ?></p>
						<?php endif; ?>
					</div>
					
					<div class="mb-6">
						<label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
						<textarea id="payment_method" name="payment_method" rows="2" 
							class="w-full px-3 py-2 border <?php echo isset($errors['payment_method']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
							placeholder="e.g. Bank transfer, details to be provided"><?php echo htmlspecialchars($paymentMethod); ?></textarea>
						<?php if (isset($errors['payment_method'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['payment_method']; ?></p>
						<?php endif; ?>
					</div>
				</div>
				
				<!-- Contact Info Tab -->
				<div data-tab-content="contact" class="hidden">
					<div class="mb-6">
						<label for="residential_address" class="block text-sm font-medium text-gray-700 mb-1">Residential Address</label>
						<textarea id="residential_address" name="residential_address" rows="3" 
							class="w-full px-3 py-2 border <?php echo isset($errors['residential_address']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($residentialAddress); ?></textarea>
						<?php if (isset($errors['residential_address'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['residential_address']; ?></p>
						<?php endif; ?>
					</div>
					
					<div class="mb-6">
						<label for="local_cell_number" class="block text-sm font-medium text-gray-700 mb-1">Local Cell Number</label>
						<input type="text" id="local_cell_number" name="local_cell_number" value="<?php echo htmlspecialchars($localCellNumber); ?>" 
							class="w-full px-3 py-2 border <?php echo isset($errors['local_cell_number']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
							placeholder="e.g. +962 791802238">
						<?php if (isset($errors['local_cell_number'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['local_cell_number']; ?></p>
						<?php endif; ?>
					</div>
					
					<div class="mb-6">
						<label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label>
						<input type="text" id="emergency_contact" name="emergency_contact" value="<?php echo htmlspecialchars($emergencyContact); ?>" 
							class="w-full px-3 py-2 border <?php echo isset($errors['emergency_contact']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
							placeholder="e.g. +962 799390614">
						<?php if (isset($errors['emergency_contact'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['emergency_contact']; ?></p>
						<?php endif; ?>
					</div>
				</div>
				
				<!-- Documents Tab -->
				<div data-tab-content="documents" class="hidden">
					<div class="mb-6">
						<label for="offer_letter" class="block text-sm font-medium text-gray-700 mb-1">Offer Letter (PDF only)</label>
						<input type="file" id="offer_letter" name="offer_letter" accept=".pdf" 
							class="w-full px-3 py-2 border <?php echo isset($errors['offer_letter']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<p class="text-xs text-gray-500 mt-1">Upload the employee's offer letter in PDF format.</p>
						<?php if (isset($errors['offer_letter'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['offer_letter']; ?></p>
						<?php endif; ?>
					</div>
					
					<div class="mb-6">
						<label for="id_front" class="block text-sm font-medium text-gray-700 mb-1">ID Front</label>
						<input type="file" id="id_front" name="id_front" accept=".jpg,.jpeg,.png,.pdf" 
							class="w-full px-3 py-2 border <?php echo isset($errors['id_front']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<p class="text-xs text-gray-500 mt-1">Upload the front of the employee's ID. Accepted formats: JPG, PNG, PDF.</p>
						<?php if (isset($errors['id_front'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['id_front']; ?></p>
						<?php endif; ?>
					</div>
					
					<div class="mb-6">
						<label for="id_back" class="block text-sm font-medium text-gray-700 mb-1">ID Back</label>
						<input type="file" id="id_back" name="id_back" accept=".jpg,.jpeg,.png,.pdf" 
							class="w-full px-3 py-2 border <?php echo isset($errors['id_back']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
						<p class="text-xs text-gray-500 mt-1">Upload the back of the employee's ID. Accepted formats: JPG, PNG, PDF.</p>
						<?php if (isset($errors['id_back'])): ?>
							<p class="text-red-500 text-xs mt-1"><?php echo $errors['id_back']; ?></p>
						<?php endif; ?>
					</div>
				</div>
				
				<!-- Form Buttons -->
				<div class="border-t border-gray-200 pt-6 mt-6">
					<div class="flex justify-end space-x-3">
						<a href="list.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-150">
							Cancel
						</a>
						<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-150">
							<i class="fas fa-save mr-2"></i> Save Employee
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<?php
// Set extra JS files
$extraJs = ['/assets/js/employee-form.js'];

// Include footer
include '../../includes/footer.php';
?>