<?php
// Include database configuration
include '../config/config.php'; // Ensure this file contains your PDO connection
include '../models/Header.php'; // Include your header file

// Function to assign a task to a user
function assignTask($pdo, $taskId, $userId) {
    $sql = "UPDATE tasks SET assigned_to = :userId WHERE id = :taskId";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':userId' => $userId,
        ':taskId' => $taskId,
    ]);
    
    return $stmt->rowCount() > 0; // Returns true if the assignment was successful
}

// Function to filter tasks assigned to a user
function getTasksByUser($pdo, $userId, $priority = null) {
    $sql = "
        SELECT tasks.*, users.username 
        FROM tasks 
        JOIN users ON tasks.assigned_to = users.id 
        WHERE tasks.assigned_to = :userId
    ";
    
    if ($priority) {
        $sql .= " AND tasks.priority = :priority"; // Add priority filter if provided
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':userId' => $userId] + ($priority ? [':priority' => $priority] : []));
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Returns an array of tasks with usernames
}

// Function to fetch all users
function getUsers($pdo) {
    $sql = "SELECT id, username FROM users"; // Ensure 'username' matches your table schema
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Returns an array of users
}

// Function to fetch all tasks
function getTasks($pdo) {
    $sql = "SELECT id, title FROM tasks"; // Adjust the query as per your tasks table
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Returns an array of tasks
}

// Function to add a new task
function addTask($pdo, $title, $description, $priority) {
    $sql = "INSERT INTO tasks (title, description, priority) VALUES (:title, :description, :priority)";
    $stmt = $pdo->prepare($sql);
    
    return $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':priority' => $priority
    ]);
}

// Handle task assignment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign_task'])) {
        $taskId = $_POST['task_id']; // Task ID from the form
        $userId = $_POST['user_id']; // User ID from the form

        if (assignTask($pdo, $taskId, $userId)) {
            $successMessage = "Task assigned successfully.";
        } else {
            $errorMessage = "Failed to assign task.";
        }
    }

    // Handle new task addition
    if (isset($_POST['add_task'])) {
        $title = $_POST['task_title'];
        $description = $_POST['task_description'];
        $priority = $_POST['task_priority'];

        if (addTask($pdo, $title, $description, $priority)) {
            $taskSuccessMessage = "Task added successfully.";
        } else {
            $taskErrorMessage = "Failed to add task.";
        }
    }

    // Handle file upload for resumes
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'C:/xampp/htdocs/taskmanager/uploads/'; // Use absolute path
        $targetFile = $uploadDir . basename($_FILES['resume']['name']);

        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $targetFile)) {
            $uploadMessage = "File uploaded successfully.";
        } else {
            $uploadMessage = "File upload failed.";
        }
    }
}

// Initialize variables for the forms
$tasks = [];
$userId = null; // Initialize userId
$users = getUsers($pdo); // Fetch users once

// Handle task filtering by user and priority
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $priority = isset($_GET['priority']) ? $_GET['priority'] : null;
    $tasks = getTasksByUser($pdo, $userId, $priority);
}

// Fetch tasks for the task assignment form
$tasksList = getTasks($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap">
    <link rel="stylesheet" href="../public/style.css"> <!-- Ensure style.css contains your Montserrat font -->
    <title>Task Assignment</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .btn.btn-primary.btn-block {
            background: #00367d;
        }
        .btn.btn-secondary.btn-block {
            background: #00367d;
        }
        .sidebar {
            height: 80vh;
            position: fixed;
            top: 186px; /* Adjust based on header height */
            left: 0;
            width: 250px;
            background-color: #f8f9fa;
            padding-top: 20px;
            border-right: 1px solid #dee2e6;
            overflow-y: auto; /* Allows scrolling if content is too tall */
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
            margin-top: 20px; /* Space below the header */
        }
        .nav-link {
            font-weight: 600;
        }
        .nav-link:hover {
            background-color: #e2e6ea;
        }
        .form-section {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="header" style="height: 56px; background-color: #343a40; color: white; display: flex; align-items: center; padding: 0 20px;">
        <h5>Task Management System</h5>
    </div>

    <div class="sidebar">
        <h4 class="text-center">Task Management</h4>
        <ul class="nav flex-column">
            <li class="nav-item form-section">
                <h5>Assign Task</h5>
                <form method="POST">
                    <div class="form-group">
                        <label for="task_id">Select Task:</label>
                        <select id="task_id" name="task_id" class="form-control" required>
                            <?php foreach ($tasksList as $task): ?>
                                <option value="<?= htmlspecialchars($task['id']) ?>" <?= (isset($_POST['task_id']) && $_POST['task_id'] == $task['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($task['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="user_id_assign">Select User:</label>
                        <select id="user_id_assign" name="user_id" class="form-control" required>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= htmlspecialchars($user['id']) ?>" <?= (isset($_POST['user_id']) && $_POST['user_id'] == $user['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" name="assign_task" class="btn btn-primary btn-block">Assign Task</button>
                </form>
                <?php if (isset($successMessage)): ?>
                    <div class='alert alert-success'><?= $successMessage ?></div>
                <?php elseif (isset($errorMessage)): ?>
                    <div class='alert alert-danger'><?= $errorMessage ?></div>
                <?php endif; ?>
            </li>

            <li class="nav-item form-section">
                <h5>Add Task</h5>
                <form method="POST">
                    <div class="form-group">
                        <label for="task_title">Task Title:</label>
                        <input type="text" id="task_title" name="task_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="task_description">Description:</label>
                        <textarea id="task_description" name="task_description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="task_priority">Priority:</label>
                        <select id="task_priority" name="task_priority" class="form-control" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <button type="submit" name="add_task" class="btn btn-primary btn-block">Add Task</button>
                </form>
                <?php if (isset($taskSuccessMessage)): ?>
                    <div class='alert alert-success'><?= $taskSuccessMessage ?></div>
                <?php elseif (isset($taskErrorMessage)): ?>
                    <div class='alert alert-danger'><?= $taskErrorMessage ?></div>
                <?php endif; ?>
            </li>

            <li class="nav-item form-section">
                <h5>Upload Resume</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="resume">Choose Resume:</label>
                        <input type="file" id="resume" name="resume" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Upload Resume</button>
                </form>
                <?php if (isset($uploadMessage)): ?>
                    <div class='alert alert-info'><?= $uploadMessage ?></div>
                <?php endif; ?>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Assigned Tasks</h2>
        <form method="GET">
            <div class="form-group">
                <label for="user_id">Filter by User:</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    <option value="">Select User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['id']) ?>" <?= ($userId == $user['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="priority">Filter by Priority:</label>
                <select id="priority" name="priority" class="form-control">
                    <option value="">All</option>
                    <option value="low" <?= (isset($_GET['priority']) && $_GET['priority'] == 'low') ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= (isset($_GET['priority']) && $_GET['priority'] == 'medium') ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= (isset($_GET['priority']) && $_GET['priority'] == 'high') ? 'selected' : '' ?>>High</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary btn-block">Filter Tasks</button>
        </form>

        <?php if (!empty($tasks)): ?>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Task Title</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= htmlspecialchars($task['description']) ?></td>
                            <td><?= htmlspecialchars($task['priority']) ?></td>
                            <td><?= htmlspecialchars($task['username']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tasks found for the selected user.</p>
        <?php endif; ?>
    </div>
</body>
</html>
