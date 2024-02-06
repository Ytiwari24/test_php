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

// Function to fetch all users
function getAllUsers()
{
    global $connection;

    $query = "SELECT * FROM user WHERE status = '1'";
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    } else {
        return "No users found.";
    }
}

// Function to add a new user
function addUser($requestData)
{
    global $connection;

    $firstName = isset($requestData['first_name']) ? htmlspecialchars($requestData['first_name']) : null;
    $lastName = isset($requestData['last_name']) ? htmlspecialchars($requestData['last_name']) : null;
    $email = isset($requestData['email']) ? filter_var($requestData['email'], FILTER_VALIDATE_EMAIL) : null;
    $password = isset($requestData['password']) ? $requestData['password'] : null;

    if (!$firstName || !$lastName || !$email || !$password) {
        http_response_code(400); // Bad Request
        return ['error' => 'Invalid or missing input data'];
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO user (first_name, last_name, email, password) VALUES ('$firstName', '$lastName', '$email', '$hashedPassword')";
    $result = $connection->query($query);

    if ($result) {
        http_response_code(201); // Created
        return ['message' => 'User created successfully'];
    } else {
        http_response_code(500); // Internal Server Error
        return ['error' => 'Failed to create user'];
    }
}

// Function to update user information
function updateUser($userId, $requestData)
{
    global $connection;

    $newFirstName = isset($requestData['first_name']) ? htmlspecialchars($requestData['first_name']) : null;
    $newLastName = isset($requestData['last_name']) ? htmlspecialchars($requestData['last_name']) : null;
    $newEmail = isset($requestData['email']) ? filter_var($requestData['email'], FILTER_VALIDATE_EMAIL) : null;

    $updateQuery = "UPDATE user SET 
                    first_name = IFNULL('$newFirstName', first_name),
                    last_name = IFNULL('$newLastName', last_name),
                    email = IFNULL('$newEmail', email)
                    WHERE id = $userId";

    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        http_response_code(200); // OK
        return ['message' => 'User updated successfully'];
    } else {
        http_response_code(500); // Internal Server Error
        return ['error' => 'Failed to update user'];
    }
}

// Function to delete a user
function deleteUser($userId)
{
    global $connection;

    $deleteQuery = "UPDATE user SET status = '0' WHERE id = $userId";
    $deleteResult = $connection->query($deleteQuery);

    if ($deleteResult) {
        http_response_code(200); // OK
        return ['message' => 'User deleted successfully'];
    } else {
        http_response_code(500); // Internal Server Error
        return ['error' => 'Failed to delete user'];
    }
}
// Function to handle login
function loginUser($requestData)
{
    global $connection;

    $email = isset($requestData['email']) ? filter_var($requestData['email'], FILTER_VALIDATE_EMAIL) : null;
    $password = isset($requestData['password']) ? $requestData['password'] : null;

    if (!$email || !$password) {
        http_response_code(400); // Bad Request
        return ['status' => 'error', 'message' => 'Invalid or missing input data'];
    }

    $query = "SELECT * FROM user WHERE email = '$email' AND status = '1'";
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Password is correct
            $response = [
                'status' => 'success',
                'message' => 'Login successful',
                'user_id' => $user['id'], // Include other user data as needed
            ];
            http_response_code(200); // OK
        } else {
            // Password is incorrect
            $response = ['status' => 'error', 'message' => 'Incorrect password'];
            http_response_code(401); // Unauthorized
        }
    } else {
        // User with the provided email not found
        $response = ['status' => 'error', 'message' => 'User not found'];
        http_response_code(404); // Not Found
    }

    return $response;
}


// Handle the request based on the HTTP method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = getAllUsers();
    echo json_encode($response);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonInput = file_get_contents('php://input');
    $requestData = json_decode($jsonInput, true);
    $response = addUser($requestData);
    echo json_encode($response);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $jsonInput = file_get_contents('php://input');
    $requestData = json_decode($jsonInput, true);
    $userId = isset($requestData['id']) ? intval($requestData['id']) : null;
    $response = updateUser($userId, $requestData);
    echo json_encode($response);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $jsonInput = file_get_contents('php://input');
    $requestData = json_decode($jsonInput, true);
    $userIdToDelete = isset($requestData['id']) ? intval($requestData['id']) : null;
    $response = deleteUser($userIdToDelete);
    echo json_encode($response);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    $jsonInput = file_get_contents('php://input');
    $requestData = json_decode($jsonInput, true);
    $response = loginUser($requestData);
    echo json_encode($response);
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method']);
}

// Close the database connection
$connection->close();
