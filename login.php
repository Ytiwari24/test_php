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
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    // Assuming you receive JSON data in the request body
    $jsonInput = file_get_contents('php://input');
    $requestData = json_decode($jsonInput, true);

    // Validate and sanitize the input data (add more validation as needed)
    $email = isset($requestData['email']) ? filter_var($requestData['email'], FILTER_VALIDATE_EMAIL) : null;
    $password = isset($requestData['password']) ? $requestData['password'] : null;

    // Validate required fields
    if (!$email || !$password) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid or missing input data']);
        exit();
    }

    // Fetch user from the users table based on the provided email
    $query = "SELECT * FROM user WHERE email = '$email' AND status = '1'";
    $result = $connection->query($query);

    // Check if a user with the provided email exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            http_response_code(200); // OK

            // Return user data in the response
            $response = [
                'message' => 'Login successful',
                'user_data' => $user,
            ];

            echo json_encode($response);
        } else {
            // Password is incorrect
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Incorrect password']);
        }
    } else {
        // User with the provided email not found
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'User not found']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method']);
}

// Close the database connection
$connection->close();
?>
