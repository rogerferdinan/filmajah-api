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
        ["email", "password", "first_name", "last_name"], 
        ["email = '$email'"],
        null,
        null
    );

    $data = [];
    if($result->num_rows > 0) {
        $row = $result->fetch_array();
        if(password_verify($pass, $row["password"])) {
            $data = [
                "email" => $row["email"],
                "first_name" => $row["first_name"],
                "last_name" => $row["last_name"]
            ];
        } else {
            $data = [];
        }
    } else {
        $data = [];
    }
    echo json_encode($data);
?>