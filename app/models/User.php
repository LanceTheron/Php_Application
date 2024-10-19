<?php
// models/User.php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Get the active logged-in user
    public function getActiveUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(); // Fetch the user record
    }

    // Fetch all users (for administrative purposes, if needed)
    public function getAllUsers() {
        $stmt = $this->pdo->prepare("SELECT * FROM users"); // Ensure the table exists
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Create a new user
    public function createUser($data) {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $stmt->execute(['name' => $data['name'], 'email' => $data['email']]);
        return $this->pdo->lastInsertId();
    }

    // Update an existing user
    public function updateUser($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->execute(['name' => $data['name'], 'email' => $data['email'], 'id' => $id]);
    }

    // Delete a user by ID
    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    // **New: Update the user's bio**
    public function updateBio($userId, $newBio) {
        $stmt = $this->pdo->prepare("UPDATE users SET bio = :bio WHERE id = :id");
        return $stmt->execute(['bio' => $newBio, 'id' => $userId]);
    }
}
?>
