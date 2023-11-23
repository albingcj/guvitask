<?php

include 'include/configSql.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["regName"];
    $email = $_POST["regMail"];
    $password = $_POST["regPwd1"];
    $passwordc = $_POST["regPwd2"];

    // check if any fields are empty
    // if (empty($name) || empty($email) || empty($password)) {
    //     $res = [
    //         'status' => 400,
    //         'message' => 'All fields are mandatory'
    //     ];
    //     echo json_encode($res);
    //     return;
    // }
    // if passwords are different
    if ($password != $passwordc) {
        $res = [
            'status' => 400,
            'message' => "Passwords don't match"
        ];
        echo json_encode($res);
        return;
    }

    // email already in db
    $checkEmailQuery = "SELECT * FROM register WHERE email = ?";
    $stmt = $db->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $res = [
            'status' => 409,
            'message' => 'Email already registered'
        ];
        echo json_encode($res);
        return;
    } else {
        // not inside the db, so we can insert to the db
        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $hashedPassword = $password;
        $insertQuery = "INSERT INTO register (name, email, password) VALUES (?, ?, ?)";
        $stmt = $db->prepare($insertQuery);
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            // Registration successful
            $res = [
                'status' => 200,
                'message' => 'Registration successful!'
            ];
            echo json_encode($res);
        } else {
            // Error in registration
            $res = [
                'status' => 500,
                'message' => 'Error : ' . $stmt->error . '. Please try again later.'
            ];
            echo json_encode($res);
        }
    }
} else {
    echo json_encode(["status" => 400, "message" => "Bad Request"]);
}
