<?php
// Include the database configuration
include '../config/config.php'; // Adjust the path if necessary

// Check if the form is submitted and task_id is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id'])) {
    $taskId = $_POST['task_id'];

    // Update the status of the task to 'completed'
    $query = "UPDATE tasks SET status = 'completed', completion_date = NOW() WHERE id = :task_id";

    try {
        // Prepare and execute the statement
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to the task management page after completion
        header("Location: #"); // Change to your main task management page
        exit;
    } catch (Exception $e) {
        error_log("Error updating task status: " . $e->getMessage(), 3, 'errors.log');
        // Handle the error as needed (e.g., show an error message)
    }
} else {
    // Redirect back or show an error if task_id is not provided
    header("Location: #"); // Change to your main task management page
    exit;
}
?>
