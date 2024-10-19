<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

include '../config/config.php';
include '../models/User.php';
include '../models/TaskModel.php';
include '../models/Header.php';

// Fetch logged-in user's data
$userModel = new User($pdo);
$activeUser = $userModel->getActiveUser($_SESSION['user_id']);

if (!$activeUser) {
    header('Location: index.php');
    exit;
}

// Handle bio update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bio'])) {
    $newBio = trim($_POST['bio']);
    $userModel->updateBio($_SESSION['user_id'], $newBio);
    $activeUser['bio'] = $newBio;
}

// Handle task filtering
$taskModel = new TaskModel($pdo);
$status = $_POST['status'] ?? '';
$priority = $_POST['priority'] ?? '';

$tasks = $status && $priority ? 
    $taskModel->getFilteredTasks($status, $priority, $_SESSION['user_id']) : [];

$totalTasks = $taskModel->getAllTasks();
$currentTask = !empty($tasks) ? $tasks[0] : null;
$availablePriorities = $status ? $taskModel->getAvailablePrioritiesByStatus($status) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Montserrat', sans-serif;
        }
        .sidebar {
            background-color: #00367D;
            color: white;
            height: 100vh;
            padding: 20px;
        }
        .sidebar h4 {
            margin-top: 0;
            color: #fff;
        }
        p {
    color: black;
}
        .form-group label {
            color: #ddd;
        }
        .dashboard-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .priority-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-left: 10px;
        }
        .priority-high { background-color: red; }
        .priority-medium { background-color: orange; }
        .priority-low { background-color: green; }

        .btn-primary {
            background-color: #fff;
            color: #00367D;
            border: none;
            font-family: 'Montserrat', sans-serif;
        }
        .btn-primary:hover {
            background-color: #002244;
        }
        .hidden { display: none; }
        .user-info h4 {
            margin-top: 20px;
        }
        #viewAllTasksBtn {
            margin-top: 10px;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 sidebar">
            <h4>Filter Tasks</h4>
            <form method="POST" id="taskFilterForm">
                <div class="form-group">
                    <label for="taskStatusSelect">Select Task Status:</label>
                    <select id="taskStatusSelect" name="status" class="form-control" onchange="updatePriorityOptions()">
                        <option value="" disabled selected>-- Choose Status --</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div id="priorityFilter" class="<?php echo $status ? '' : 'hidden'; ?>">
                    <div class="form-group">
                        <label for="taskPrioritySelect">Select Task Priority:</label>
                        <select id="taskPrioritySelect" name="priority" class="form-control">
                            <option value="" disabled selected>-- Choose Priority --</option>
                            <?php foreach ($availablePriorities as $priorityOption): ?>
                                <option value="<?php echo htmlspecialchars($priorityOption); ?>">
                                    <?php echo htmlspecialchars(ucfirst($priorityOption)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </form>

            <?php if (!empty($tasks)): ?>
                <h4>Your Task:</h4>
                <div class="list-group-item">
                    <h5>
                        <a href="taskDetail.php?id=<?php echo htmlspecialchars($currentTask['id']); ?>">
                            <?php echo htmlspecialchars($currentTask['Task_name']); ?>
                        </a>
                        <span>(<?php echo htmlspecialchars($currentTask['status']); ?>)</span>
                        <span>[<?php echo htmlspecialchars($currentTask['priority']); ?>]</span>
                        <span class="priority-indicator priority-<?php echo strtolower(htmlspecialchars($currentTask['priority'])); ?>"></span>
                    </h5>
                    <p><?php echo htmlspecialchars($currentTask['description']); ?></p>
                </div>
            <?php else: ?>
                <p>No tasks available based on the selected filters.</p>
            <?php endif; ?>

            <h4>Total Tasks: <?php echo count($totalTasks); ?></h4>
            <button class="btn btn-primary w-100" id="viewAllTasksBtn" onclick="viewAllTasks()">View All Tasks</button>

            <div id="allTasksDisplay" class="hidden">
                <h4>All Tasks:</h4>
                <div class="list-group">
                    <?php foreach ($tasks as $task): ?>
                        <div class="list-group-item">
                            <h5>
                                <a href="taskDetail.php?id=<?php echo htmlspecialchars($task['id']); ?>">
                                    <?php echo htmlspecialchars($task['Task_name']); ?>
                                </a>
                            </h5>
                            <p><?php echo htmlspecialchars($task['description']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="dashboard-container mt-4">
                <h2>Welcome, <?php echo htmlspecialchars($activeUser['username']); ?>!</h2>

                <div class="user-info">
                    <h4>Your Information:</h4>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($activeUser['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($activeUser['email']); ?></p>
                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($activeUser['contact_number']); ?></p>
                    <p><strong>Registered On:</strong> <?php echo htmlspecialchars($activeUser['created_at']); ?></p>

                    <h4>Biography:</h4>
                    <p id="bioDisplay"><?php echo htmlspecialchars($activeUser['bio']); ?></p>

                    <form method="POST" id="bioForm" class="hidden">
                        <div class="form-group">
                            <textarea id="bio" name="bio" class="form-control"><?php echo htmlspecialchars($activeUser['bio']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Bio</button>
                        <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Cancel</button>
                    </form>

                    <button id="editBioBtn" class="btn btn-primary" onclick="editBio()">Edit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updatePriorityOptions() {
        const priorityFilter = document.getElementById('priorityFilter');
        priorityFilter.classList.toggle('hidden', !document.getElementById('taskStatusSelect').value);
    }

    function viewAllTasks() {
        document.getElementById('allTasksDisplay').classList.toggle('hidden');
    }

    function editBio() {
        document.getElementById('bioDisplay').classList.add('hidden');
        document.getElementById('bioForm').classList.remove('hidden');
        document.getElementById('editBioBtn').classList.add('hidden');
    }

    function cancelEdit() {
        document.getElementById('bioDisplay').classList.remove('hidden');
        document.getElementById('bioForm').classList.add('hidden');
        document.getElementById('editBioBtn').classList.remove('hidden');
    }
</script>

</body>
</html>
