<?php

// Database credentials
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'patri_samaj';

// Create a connection to the MySQL database
$connection = new mysqli($host, $username, $password, $database);

// Check the connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch data from the users table
    $query = "SELECT * FROM user WHERE status = '1'";
    $result = $connection->query($query);

    // Check if there are results
    if ($result->num_rows > 0) {
        // Fetch data and store in an array
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        // Set the Content-Type header to JSON
        header('Content-Type: application/json');

        // Output the user data as JSON
        echo json_encode($users);
    } else {
        echo "No users found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you receive JSON data in the request body
    $jsonInput = file_get_contents('php://input');
    $requestData = json_decode($jsonInput, true);

    // Validate and sanitize the input data (add more validation as needed)
    $firstName = isset($requestData['first_name']) ? htmlspecialchars($requestData['first_name']) : null;
    $lastName = isset($requestData['last_name']) ? htmlspecialchars($requestData['last_name']) : null;
    $email = isset($requestData['email']) ? filter_var($requestData['email'], FILTER_VALIDATE_EMAIL) : null;
    $password = isset($requestData['password']) ? $requestData['password'] : null; // Assuming password is included in the JSON payload
    // ... Add more fields as needed

    // Validate required fields
    if (!$firstName || !$lastName || !$email || !$password) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid or missing input data']);
        exit();
    }

    // Hash the password using Bcrypt
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user into the users table
    $query = "INSERT INTO user (first_name, last_name, email, password) VALUES ('$firstName', '$lastName', '$email', '$hashedPassword')";
    $result = $connection->query($query);

    if ($result) {
        http_response_code(201); // Created
        echo json_encode(['message' => 'User created successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to create user']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Assuming you receive JSON data in the request body
    $jsonInput = file_get_contents('php://input');
    $requestData = json_decode($jsonInput, true);

    // Validate and sanitize the input data (add more validation as needed)
    $userId = isset($requestData['id']) ? intval($requestData['id']) : null;
    $newFirstName = isset($requestData['first_name']) ? htmlspecialchars($requestData['first_name']) : null;
    $newLastName = isset($requestData['last_name']) ? htmlspecialchars($requestData['last_name']) : null;
    $newEmail = isset($requestData['email']) ? filter_var($requestData['email'], FILTER_VALIDATE_EMAIL) : null;
    // ... Add more fields as needed

    // Validate required fields
    // if (!$userId || (!$newFirstName && !$newLastName && !$newEmail)) {
    //     http_response_code(400); // Bad Request
    //     echo json_encode(['error' => 'Invalid or missing input data']);
    //     exit();
    // }

    // Update user information in the users table
    $updateQuery = "UPDATE user SET 
                    first_name = IFNULL('$newFirstName', first_name),
                    last_name = IFNULL('$newLastName', last_name),
                    email = IFNULL('$newEmail', email)
                    WHERE id = $userId";

    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        http_response_code(200); // OK
        echo json_encode(['message' => 'User updated successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to update user']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Assuming you receive JSON data in the request body
    $jsonInput = file_get_contents('php://input');
    $requestData = json_decode($jsonInput, true);

    // Validate and sanitize the input data (add more validation as needed)
    $userIdToDelete = isset($requestData['id']) ? intval($requestData['id']) : null;

    // Validate required fields
    if (!$userIdToDelete) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid or missing user ID']);
        exit();
    }

    // Delete user from the users table
    $deleteQuery = "UPDATE user SET status = '0' WHERE id = $userIdToDelete";
    $deleteResult = $connection->query($deleteQuery);

    if ($deleteResult) {
        http_response_code(200); // OK
        echo json_encode(['message' => 'User deleted successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to delete user']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method']);
}

// Close the database connection
$connection->close();
