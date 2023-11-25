<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'guvitask');
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


function validateCredentials($email, $password)
{
    global $mysqli;
    $query = "SELECT * FROM register WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            require_once("../vendor/autoload.php");
            $redis = new Predis\Client();

            // $redisKey = "user:email";
            // $redis->hmset($redisKey, "id", $user['id'], "name", $user['name'], "email", $user['email']);
            $redis->set("user:email", $user['email']);
            $redis->set("user:name", $user['name']);
            return true;
        }
    }

    return false;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userEmail = $_POST['userEmail'];
    $userPwd = $_POST['userPwd'];


    
    $validCredentials = validateCredentials($userEmail, $userPwd);

    if ($validCredentials) {
        $res = [
            'status' => 200,
            'message' => "Login successful",
            'email' => $userEmail,
            'password' => $userPwd
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
    return;
}


