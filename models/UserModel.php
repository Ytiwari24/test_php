<?php

class UserModel {
    private static $connection;

    // Establish the database connection
    private static function connect() {
        require_once('../config/database.php');

        // Create a connection to the MySQL database
        self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // Check the connection
        if (self::$connection->connect_error) {
            die("Connection failed: " . self::$connection->connect_error);
        }
    }

    // Close the database connection
    private static function close() {
        if (self::$connection) {
            self::$connection->close();
        }
    }

    public static function getAllUsers() {
        self::connect();

        $query = "SELECT * FROM user WHERE status = '1'";
        $result = self::$connection->query($query);

        $users = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }

        self::close();
        return $users;
    }

    public static function createUser($data) {
        self::connect();

        $firstName = self::$connection->real_escape_string($data['first_name']);
        $lastName = self::$connection->real_escape_string($data['last_name']);
        $email = self::$connection->real_escape_string($data['email']);
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $query = "INSERT INTO user (first_name, last_name, email, password) 
                  VALUES ('$firstName', '$lastName', '$email', '$hashedPassword')";
        $result = self::$connection->query($query);

        self::close();
        return $result;
    }

    public static function updateUser($userId, $data) {
        self::connect();

        $userId = self::$connection->real_escape_string($userId);
        $newFirstName = self::$connection->real_escape_string($data['first_name']);
        $newLastName = self::$connection->real_escape_string($data['last_name']);
        $newEmail = self::$connection->real_escape_string($data['email']);

        $updateQuery = "UPDATE user SET 
                        first_name = '$newFirstName',
                        last_name = '$newLastName',
                        email = '$newEmail'
                        WHERE id = $userId";
        $updateResult = self::$connection->query($updateQuery);

        self::close();
        return $updateResult;
    }

    public static function deleteUser($userId) {
        self::connect();

        $userId = self::$connection->real_escape_string($userId);

        $deleteQuery = "UPDATE user SET status = '0' WHERE id = $userId";
        $deleteResult = self::$connection->query($deleteQuery);

        self::close();
        return $deleteResult;
    }
}
