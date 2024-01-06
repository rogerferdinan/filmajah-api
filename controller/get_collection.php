<?php
    header('Content-Type: application/json; charset=utf-8');
    $post = json_decode(file_get_contents('php://input'), true);
    $email = (isset($post) && array_key_exists("email", $post)) ? $post["email"] : "";
    if($email === "") {
        echo json_encode([
            "is_success" => False,
            "message" => "Email cannot be empty"
        ]);
        exit();
    }
    // sanitize input
    $email = $GLOBALS["db"]->sanitize($email);

    ['result'=>$result,'error'=>$error] = $GLOBALS["db"]->select(
        "collection c", 
        [
            "JOIN movies m" => "m.movie_id = c.movie_id",
        ], [
            "m.movie_name", 
            "m.image_url"
        ], [
            "email = '$email'"
        ],
    );
    $data = array();
    while($row = $result->fetch_array()) {
        $data[] = [
            "movie_name" => $row["movie_name"],
            "img_url" => $row["image_url"]
        ];
    }
    echo json_encode($data);
?>