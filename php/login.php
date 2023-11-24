<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'guvitask');
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $userEmail = $_POST['userEmail'];
    $userPwd = $_POST['userPwd'];

    // Validate user credentials
    $validCredentials = validateCredentials($userEmail, $userPwd);

    if ($validCredentials) {
        $res = [
            'status' => 200,
            'message' => "Login successful"
        ];
        echo json_encode($res);
        return;
    } else {
        $res = [
            'status' => 400,
            'message' => "Incorrect email or password"
        ];
        echo json_encode($res);
        return;
    }
} else {
    $res = [
        'status' => 400,
        'message' => 'Bad Request'
    ];
    echo json_encode($res);
}

// Function to validate user credentials
function validateCredentials($email, $password)
{
    // Access the global database connection variable
    global $mysqli;

    // Query to retrieve the user's hashed password by email
    $query = "SELECT * FROM register WHERE email = ? LIMIT 1";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Check if the provided password matches the hashed password in the database
        return password_verify($password, $user['password']);
    }

    return false;
}
