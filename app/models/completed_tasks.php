<?php
// Include the database configuration
include '../config/config.php'; // Adjust the path if necessary
include '../models/Header.php'; // Header Included

// Initialize empty arrays to hold completed and pending tasks
$completedTasks = [];
$pendingTasks = [];

// Query to fetch completed tasks
$queryCompleted = "SELECT * FROM tasks WHERE status = 'completed'";

try {
    // Prepare and execute the statement for completed tasks
    $stmtCompleted = $pdo->prepare($queryCompleted);
    $stmtCompleted->execute();

    // Check if any completed tasks are returned
    if ($stmtCompleted->rowCount() > 0) {
        // Fetch all completed tasks
        $completedTasks = $stmtCompleted->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error executing query: " . $e->getMessage(), 3, 'errors.log');
}

// Query to fetch pending tasks
$queryPending = "SELECT * FROM tasks WHERE status IN ('pending', 'not started')";

try {
    // Prepare and execute the statement for pending tasks
    $stmtPending = $pdo->prepare($queryPending);
    $stmtPending->execute();

    // Check if any pending tasks are returned
    if ($stmtPending->rowCount() > 0) {
        // Fetch all pending tasks
        $pendingTasks = $stmtPending->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error executing query: " . $e->getMessage(), 3, 'errors.log');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        h1, h2 {
            margin-top: 20px; 
        }
        .header {
            height: 56px; 
            background-color: #343a40; 
            color: white; 
            display: flex; 
            align-items: center; 
            padding: 0 20px;
            border-radius: 5px 5px 0 0;
        }
        .task-card {
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            background-color: white;
        }
        .task-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        .completion-date {
            font-size: 0.9em;
            color: #666;
        }
        .badge-status {
            font-size: 0.75em;
            border-radius: 15px;
            padding: 5px 10px;
        }
        .btn-complete {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
        }
        .btn-complete:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container-fluid">

        <div class="header">
            <h5>Task Management Dashboard</h5>
        </div>

        <div class="row">
            <!-- Completed Tasks Column -->
            <div class="col-md-6">
                <h2>Completed Tasks</h2>
                <div class="task-card p-3">
                    <ul class="list-group">
                        <?php if (!empty($completedTasks)): ?>
                            <?php foreach ($completedTasks as $task): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($task['Task_name']); ?></strong>
                                        <div class="completion-date"><?php echo htmlspecialchars($task['completion_date']); ?></div>
                                    </div>
                                    <span class="badge badge-success badge-status">Completed</span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-warning">No completed tasks found.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Pending Tasks Column -->
            <div class="col-md-6">
                <h2>Pending Tasks</h2>
                <div class="task-card p-3">
                    <ul class="list-group">
                        <?php if (!empty($pendingTasks)): ?>
                            <?php foreach ($pendingTasks as $task): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($task['Task_name']); ?></strong>
                                    </div>
                                    <form method="post" action="complete_task.php" class="ml-auto">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" class="btn btn-complete btn-sm"><i class="fas fa-check"></i> Mark as Completed</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-warning">No pending tasks found.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
