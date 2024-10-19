<?php
session_start();
include '../config/config.php';
include '../models/TaskModel.php';

$taskModel = new TaskModel($pdo);

// Check for a selected task status from the AJAX request
$statusFilter = isset($_POST['status']) ? $_POST['status'] : '';
if ($statusFilter) {
    $tasks = $taskModel->getTasksByStatus($statusFilter); // Fetch tasks based on selected status
} else {
    $tasks = $taskModel->getAllTasks(); // Fetch all tasks if no status is selected
}

// Generate the HTML for tasks
foreach ($tasks as $task) {
    echo '<li>' . htmlspecialchars($task['Task_name']) . ' - ' . htmlspecialchars($task['description']) . '</li>';
}
?>
