<?php

class UserController {
    public static function getAllUsers() {
        $users = UserModel::getAllUsers();
        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public static function createUser() {
        $jsonInput = file_get_contents('php://input');
        $requestData = json_decode($jsonInput, true);

        $result = UserModel::createUser($requestData);

        if ($result) {
            http_response_code(201); // Created
            echo json_encode(['message' => 'User created successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to create user']);
        }
    }

    public static function updateUser($userId) {
        $jsonInput = file_get_contents('php://input');
        $requestData = json_decode($jsonInput, true);

        $result = UserModel::updateUser($userId, $requestData);

        if ($result) {
            http_response_code(200); // OK
            echo json_encode(['message' => 'User updated successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to update user']);
        }
    }

    public static function deleteUser($userId) {
        $result = UserModel::deleteUser($userId);

        if ($result) {
            http_response_code(200); // OK
            echo json_encode(['message' => 'User deleted successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to delete user']);
        }
    }
}
