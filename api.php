<?php

// Sample data (you can replace this with actual data fetching logic)
$users = [
    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
    ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
    ['id' => 3, 'name' => 'Bob Smith', 'email' => 'bob@example.com'],
];


header('Content-Type: application/json');

 echo json_encode($users);

 //URL  http://localhost/test_php/api.php