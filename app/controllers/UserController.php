<?php
// controllers/UserController.php
class UserController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        echo json_encode($users);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = $this->userModel->createUser($data);
        echo json_encode(["message" => "User created successfully!", "id" => $userId]);
    }

    public function update() {
        $id = $_GET['id']; // Expect ID as a query parameter
        $data = json_decode(file_get_contents("php://input"), true);
        $this->userModel->updateUser($id, $data);
        echo json_encode(["message" => "User updated successfully!"]);
    }

    public function delete() {
        $id = $_GET['id']; // Expect ID as a query parameter
        $this->userModel->deleteUser($id);
        echo json_encode(["message" => "User deleted successfully!"]);
    }
}

