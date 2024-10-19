<?php
// Define the base directory for includes (optional)
define('BASE_DIR', __DIR__ . '/../'); // Adjust this path as necessary

// Include the TaskModel.php file using an absolute path
require_once BASE_DIR . 'models/TaskModel.php';

// Alternatively, you can use a relative path if it is correct
// require_once '../models/TaskModel.php';

// TaskController class definition
class TaskController {
    
    // Method to retrieve all tasks
    public function getAllTasks() {
        // Create an instance of TaskModel
        $taskModel = new TaskModel();
        // Fetch all tasks
        $tasks = $taskModel->getAllTasks();
        return $tasks; // Return the fetched tasks
    }
    
    // Method to add a new task
    public function addTask($title, $description, $status, $priority, $due_date) {
        $taskModel = new TaskModel();
        return $taskModel->addTask($title, $description, $status, $priority, $due_date);
    }

    // Method to update an existing task
    public function updateTask($id, $title, $description, $status, $priority, $due_date) {
        $taskModel = new TaskModel();
        return $taskModel->updateTask($id, $title, $description, $status, $priority, $due_date);
    }

    // Method to delete a task by ID
    public function deleteTask($id) {
        $taskModel = new TaskModel();
        return $taskModel->deleteTask($id);
    }
}

// Example usage of the TaskController
$controller = new TaskController();
$allTasks = $controller->getAllTasks(); // Fetch all tasks

// Further processing or output can be done here
if ($allTasks) {
    foreach ($allTasks as $task) {
        echo "Task ID: " . $task['id'] . "<br>";
        echo "Title: " . $task['title'] . "<br>";
        echo "Description: " . $task['description'] . "<br>";
        echo "Status: " . $task['status'] . "<br>";
        echo "Priority: " . $task['priority'] . "<br>";
        echo "Due Date: " . $task['due_date'] . "<br><br>";
    }
} else {
    echo "No tasks found.";
}
