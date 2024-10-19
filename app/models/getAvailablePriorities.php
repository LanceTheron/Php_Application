<?php
include '../config/config.php';
include '../models/TaskModel.php';

$taskModel = new TaskModel($pdo);

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    
    // Debug: Check what status is being received
    error_log("Fetching priorities for status: $status");

    // Fetch available priorities based on the selected status
    $availablePriorities = $taskModel->getAvailablePrioritiesByStatus($status);
    
    // Debug: Log the priorities fetched
    error_log("Available priorities: " . json_encode($availablePriorities));

    echo json_encode($availablePriorities);
} else {
    echo json_encode([]); // Return an empty array if no status is provided
}
?>
