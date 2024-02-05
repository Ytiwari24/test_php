<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Display</title>
</head>

<body>

    <?php
    // Sample data (you can replace this with actual data fetching logic)
    $users = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
        ['id' => 3, 'name' => 'Bob Smith', 'email' => 'bob@example.com'],
    ];

    // Display data in a table
    echo '<h2>User List</h2>';
    echo '<table border="1">';
    echo '<tr><th>ID</th><th>Name</th><th>Email</th></tr>';

    foreach ($users as $user) {
        echo '<tr>';
        echo '<td>' . $user['id'] . '</td>';
        echo '<td>' . $user['name'] . '</td>';
        echo '<td>' . $user['email'] . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    ?>

</body>

</html>