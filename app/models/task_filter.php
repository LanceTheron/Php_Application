<?php
session_start();
include '../config/config.php';
include '../models/TaskModel.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}

// Fetch tasks based on status
$taskModel = new TaskModel();
$status = isset($_POST['status']) ? $_POST['status'] : '';
$tasks = $taskModel->getAllTasks($status);

// Return tasks as JSON
header('Content-Type: application/json');
echo json_encode(['tasks' => $tasks]);
