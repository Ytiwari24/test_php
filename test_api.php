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

// Fetch data from the users table
$query = "SELECT * FROM user";
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

// Close the database connection
$connection->close();
