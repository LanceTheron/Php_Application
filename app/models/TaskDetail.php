<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

include '../config/config.php';
include '../models/TaskModel.php';
include '../models/Header.php';

// Get the task ID from the URL
$taskId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch task details
$taskModel = new TaskModel($pdo);
$taskDetails = $taskModel->getTaskById($taskId); // Create this method in your TaskModel

if (!$taskDetails) {
    echo "Task not found.";
    exit;
}

// Handle the form submission to mark the task as completed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_task'])) {
    // Update task status in the database
    $taskModel->markTaskAsCompleted($taskId); // Create this method in your TaskModel
    // Refresh the task details after updating
    $taskDetails = $taskModel->getTaskById($taskId); // Fetch updated task details
}

// Check if resume exists
$resumePath = $taskDetails['resume_path'] ?? null; // Assuming 'resume_path' is the column name in your tasks table
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8; /* Soft light background */
            font-family: 'Montserrat', sans-serif; /* Elegant font */
        }
        .container {
            margin-top: 40px;
            background: white; /* White background for the container */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1); /* Enhanced shadow for depth */
        }
        h2 {
            color: #343a40; /* Dark color for headings */
            margin-bottom: 20px;
            font-weight: 700; /* Bold for prominence */
        }
        h4 {
            color: #007bff; /* Primary color for subheadings */
            margin-top: 30px;
            font-weight: 600; /* Semi-bold for contrast */
        }
        .list-group-item {
            border: none; /* Remove border for a cleaner look */
            padding: 15px 20px; /* Ample padding for touch targets */
        }
        .requirement-checked {
            text-decoration: line-through;
            color: #6c757d; /* Soft grey for completed requirements */
        }
        .button-group {
            margin-top: 20px;
        }
        .btn {
            border-radius: 50px; /* Rounded buttons for elegance */
            padding: 10px 20px; /* Consistent padding */
        }
        .btn-primary {
            background-color: #007bff; /* Custom primary button color */
            border: none; /* Remove border */
            transition: background-color 0.3s; /* Smooth transition */
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }
        .btn-success {
            background-color: #28a745; /* Custom success button color */
            border: none; /* Remove border */
            transition: background-color 0.3s; /* Smooth transition */
        }
        .btn-success:hover {
            background-color: #218838; /* Darker shade on hover */
        }
        .btn-info {
            background-color: #17a2b8; /* Custom info button color */
            border: none; /* Remove border */
            transition: background-color 0.3s; /* Smooth transition */
        }
        .btn-info:hover {
            background-color: #138496; /* Darker shade on hover */
        }
        .btn-secondary {
            background-color: #6c757d; /* Custom secondary button color */
            border: none; /* Remove border */
            transition: background-color 0.3s; /* Smooth transition */
        }
        .btn-secondary:hover {
            background-color: #5a6268; /* Darker shade on hover */
        }
        .card {
            border: none; /* No border for card */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1); /* Soft shadow */
            margin-bottom: 20px; /* Space between cards */
        }
        .resume-section {
            padding: 20px;
            background-color: #e9ecef; /* Light grey background for the resume section */
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2><?php echo htmlspecialchars($taskDetails['Task_name']); ?></h2>
        <div class="card">
            <div class="card-body">
                <p><strong>Description:</strong> <?php echo htmlspecialchars($taskDetails['description']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($taskDetails['status']); ?></p>
                <p><strong>Created On:</strong> <?php echo htmlspecialchars($taskDetails['created_at']); ?></p>
                <p><strong>Due Date:</strong> <?php echo htmlspecialchars($taskDetails['due_date']); ?></p>
            </div>
        </div>

        <!-- New section for requirements -->
        <h4>Requirements:</h4>
        <form method="post">
            <ul class="list-group mb-3">
                <?php 
                // Check if requirements exist
                if (isset($taskDetails['requirements'])) {
                    $requirements = explode(',', $taskDetails['requirements']);
                    foreach ($requirements as $index => $requirement): ?>
                        <li class="list-group-item">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="requirements[]" value="<?php echo htmlspecialchars(trim($requirement)); ?>" <?php echo (in_array(trim($requirement), $taskDetails['completed_requirements'] ?? [])) ? 'checked' : ''; ?> />
                                <label class="form-check-label <?php echo (in_array(trim($requirement), $taskDetails['completed_requirements'] ?? [])) ? 'requirement-checked' : ''; ?>">
                                    <?php echo htmlspecialchars(trim($requirement)); ?>
                                </label>
                            </div>
                        </li>
                    <?php endforeach; 
                } else {
                    echo "<li class='list-group-item'>No requirements specified for this task.</li>";
                }
                ?>
            </ul>

            <div class="button-group">
                <button type="submit" name="complete_requirements" class="btn btn-primary">Complete Requirements</button>
                <button type="submit" name="complete_task" class="btn btn-success">Mark as Completed</button>
            </div>
        </form>

        <!-- Resume Display Section -->
        <h4>Uploaded Resume:</h4>
        <div class="resume-section">
            <?php if ($resumePath && file_exists($resumePath)): ?>
                <a href="<?php echo htmlspecialchars($resumePath); ?>" target="_blank" class="btn btn-info"><i class="fas fa-file-pdf"></i> View Resume</a>
            <?php else: ?>
                <p>No resume uploaded for this task.</p>
            <?php endif; ?>
        </div>

        <!-- Button Group for actions -->
        <div class="button-group mt-4">
            <a href="UserDashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
