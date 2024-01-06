<?php
    header('Content-Type: application/json; charset=utf-8');
    $post = json_decode(file_get_contents('php://input'), true);
    $email = (isset($post) && array_key_exists("email", $post)) ? $post["email"] : "";
    $first_name = (isset($post) && array_key_exists("first_name", $post)) ? $post["first_name"] : "";
    $last_name = (isset($post) && array_key_exists("last_name", $post)) ? $post["last_name"] : "";
    $pass = (isset($post) && array_key_exists("password", $post)) ? $post["password"] : "";
    $pass = password_hash($pass, null, ["cost" => 14]);

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
    if($first_name === "") {
        echo json_encode([
            "is_success" => False,
            "message" => "First name cannot be empty"
        ]);
        exit();
    }
    if($last_name === "") {
        echo json_encode([
            "is_success" => False,
            "message" => "Last name cannot be empty"
        ]);
        exit();
    }
    // Sanitize Input
    $email = $GLOBALS["db"]->sanitize($email);
    $first_name = $GLOBALS["db"]->sanitize($first_name);
    $last_name = $GLOBALS["db"]->sanitize($last_name);
    $pass = $GLOBALS["db"]->sanitize($pass);

    // Check if email already used
    ['result'=>$result,'error'=>$error] = $GLOBALS["db"]->select(
        "users", 
        null,
        ["email"],
        ["email = '$email'"]
    );
    
    $data = array();
    if($result->num_rows > 0) {
        $data = [
            "is_success" => False,
            "message" => "Email is already registered"
        ];
        echo json_encode($data);
        exit();
    }

    ['result'=>$result,'error'=>$error] = $GLOBALS["db"]->insert(
        "users", 
        [
            "email"=>$email,
            "first_name"=>$first_name,
            "last_name"=>$last_name,
            "password"=>$pass,
        ]
    );
    if($result === TRUE) {
        $data = [
            "is_success" => TRUE,
            "message" => "Success to register new account"
        ];
    } else {
        $data = [
            "is_success" => FALSE,
            "message" => $mysqli->error
        ];
    }
    echo json_encode($data);
?>