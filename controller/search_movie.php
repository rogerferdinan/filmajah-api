<?php
    header('Content-Type: application/json; charset=utf-8');
    $post = json_decode(file_get_contents('php://input'), true);
    $query = (isset($post) && array_key_exists("query", $post)) ? $post["query"] : "";

    ['result'=>$result,'error'=>$error] = $GLOBALS["db"]->select(
        "movies m", 
        null, 
        [
            "m.movie_name",
            "m.rating",
            "m.image_url"
        ], [
            "movie_name LIKE '%$query%'"
        ]
    );

    $data = array();
    while($row = $result->fetch_array()) {
        $data[] = [
            "movie_name" => $row["movie_name"],
            "rating" => (float)$row["rating"],
            "img_url" => $row["image_url"]
        ];
    }
    echo json_encode($data);
?>