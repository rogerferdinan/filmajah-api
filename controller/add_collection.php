<?php
    header('Content-Type: application/json; charset=utf-8');
    $post = json_decode(file_get_contents('php://input'), true);
    $email = (isset($post) && array_key_exists("email", $post)) ? $post["email"] : "";
    $pass = (isset($post) && array_key_exists("password", $post)) ? $post["password"] : "";
    $movie_name = (isset($post) && array_key_exists("movie_name", $post) ? $post["movie_name"] : "");
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
    if($movie_name === "") {
        echo json_encode([
            "is_success" => False,
            "message" => "Movie name cannot be empty"
        ]);
        exit();
    }
    // sanitize input
    $email = $GLOBALS["db"]->sanitize($email);
    $pass = $GLOBALS["db"]->sanitize($pass);
    $movie_name = $GLOBALS["db"]->sanitize($movie_name);

    // Query to User table
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
    if($result->num_rows > 0 && password_verify($pass, $result->fetch_array()["password"])) {
        ['result'=>$collection_result,'error'=>$error] = $GLOBALS["db"]->select(
            "movies",
            null,
            ["movie_id", "movie_name"],
            ["movie_name = '$movie_name'"],
            null,
            null
        );
        $movie_id = $collection_result->fetch_array()["movie_id"];
        ['result'=>$result,'error'=>$error] = $GLOBALS["db"]->insert(
            "collection",
            [
                "email"=>$email,
                "movie_id"=>$movie_id
            ]
        );
        if($error != null) {
            http_response_code(500);
            echo json_decode($error);
            exit();
        }
        echo json_encode([
            "is_success" => TRUE,
            "message" => "Success to add new collection"
        ]);
    } else {
        echo json_encode([
            "is_success" => FALSE,
            "message" => "Wrong credentials"
        ]);
    }
?>