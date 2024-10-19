<?php
class TaskModel {
    private $db;

    // Constructor to initialize the database connection
    public function __construct($pdo) {
        $this->db = $pdo; // Assuming $pdo is passed from the controller
    }

    // Method to retrieve all tasks or filter by status and priority
    public function getAllTasks($status = null, $priority = null) {
        $query = "SELECT * FROM tasks WHERE 1=1"; // Always true condition for flexible filtering

        // If a status is provided, modify the query to filter tasks
        if ($status) {
            $query .= " AND status = :status";
        }
        // If a priority is provided, modify the query to filter tasks
        if ($priority) {
            $query .= " AND priority = :priority";
        }

        $stmt = $this->db->prepare($query);

        // Bind the status parameter if filtering by status
        if ($status) {
            $stmt->bindValue(':status', $status);
        }
        // Bind the priority parameter if filtering by priority
        if ($priority) {
            $stmt->bindValue(':priority', $priority);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return tasks as an associative array
    }

    // New method to retrieve filtered tasks
    public function getFilteredTasks($status = null, $priority = null) {
        return $this->getAllTasks($status, $priority); // Use existing method to filter tasks
    }

    // Method to retrieve a single task by ID
    public function getTaskById($id) {
        $query = "SELECT * FROM tasks WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Return a single task as an associative array
    }

    // Method to add a new task
    public function addTask($title, $description, $requirements, $status, $priority, $due_date, $userId = null) {
        $query = "INSERT INTO tasks (title, description, requirements, status, priority, due_date" . 
                 ($userId ? ", user_id" : "") . ") VALUES (:title, :description, :requirements, :status, :priority, :due_date" . 
                 ($userId ? ", :user_id" : "") . ")";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':requirements', $requirements);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':due_date', $due_date);
        if ($userId) {
            $stmt->bindParam(':user_id', $userId);
        }
        return $stmt->execute(); // Returns true on success
    }

    // Method to update an existing task
    public function updateTask($id, $title, $description, $requirements, $status, $priority, $due_date) {
        $query = "UPDATE tasks SET title = :title, description = :description, requirements = :requirements, 
                  status = :status, priority = :priority, due_date = :due_date WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':requirements', $requirements);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':due_date', $due_date);
        return $stmt->execute(); // Returns true on success
    }

    // Method to delete a task by ID
    public function deleteTask($id) {
        $query = "DELETE FROM tasks WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute(); // Returns true on success
    }

    // Method to mark a task as completed
    public function markTaskAsCompleted($id) {
        $query = "UPDATE tasks SET status = 'Completed' WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute(); // Returns true on success
    }

    // Method to count tasks based on their status
    public function getTaskCount($status) {
        $query = "SELECT COUNT(*) FROM tasks WHERE status = :status"; // Removed user_id filtering
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchColumn(); // Return the count of tasks
    }

    // Method to retrieve available priorities based on task status
    public function getAvailablePrioritiesByStatus($status) {
        $query = "SELECT DISTINCT priority FROM tasks WHERE status = :status";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Return an array of distinct priorities
    }

    // Method to retrieve the highest priority task for a user
    public function getHighestPriorityTaskForUser($userId) {
        $query = "SELECT * FROM tasks WHERE user_id = :user_id ORDER BY priority DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Return the highest priority task
    }
}
?>
