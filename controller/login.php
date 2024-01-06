<?php
    header('Content-Type: application/json; charset=utf-8');
    $post = json_decode(file_get_contents('php://input'), true);
    $email = (isset($post) && array_key_exists("email", $post)) ? $post["email"] : "";
    $pass = (isset($post) && array_key_exists("password", $post)) ? $post["password"] : "";
    if($email === "") {
        echo json_encode([
            "is_success" => False,
            "message" => "Email cannot be empty"
        ]);
        exit();
    }
    if($pass === "") {
        echo json_encode([
            "is_success" => False,
            "message" => "Password cannot be empty"
        ]);
        exit();
    }
    // sanitize input
    $email = $GLOBALS["db"]->sanitize($email);
    $pass = $GLOBALS["db"]->sanitize($pass);
    // Query
    ['result'=>$result,'error'=>$error] = $GLOBALS["db"]->select(
        "users", 
        null,
        ["email", "password"], 
        ["email = '$email'"],
        null,
        null
    );

    if($error != null) {
        http_response_code(500);
        echo json_decode($error);
        exit();
    }

    $data = array();
    if($result->num_rows > 0) {
        if(password_verify($pass, $result->fetch_array()["password"])) {
            $data = [
                "is_success" => TRUE,
                "message" => "Login succeed"
            ];
        } else {
            $data = [
                "is_success" => FALSE,
                "message" => "Password is incorrect"
            ];
        }
    } else if($result->num_rows == 0) {
        $data = [
            "is_success" => FALSE,
            "message" => "User not found"
        ];
    } else {
        $data = [
            "is_success" => FALSE,
            "message" => $mysqli->error
        ];
    }
    echo json_encode($data);
?>