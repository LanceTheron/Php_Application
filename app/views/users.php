<?php
include '../config/config.php'; // Update the path if necessary

// Ensure that the connection was established
if (!isset($conn)) {
    die("Database connection not established.");
}

$query = "SELECT id, name, email FROM users";
$result = mysqli_query($conn, $query);

$users = []; // Initialize the variable
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row; // Populate the users array
    }
} else {
    // Handle query error
    echo "Error fetching users: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Users</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($users) && is_array($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <!-- Add buttons for editing and deleting -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- Add pagination controls -->
</body>
</html>
