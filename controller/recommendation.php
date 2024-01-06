<?php
    header('Content-Type: application/json; charset=utf-8');

    ['result'=>$result,'error'=>$error] = $GLOBALS["db"]->select(
        "movies m", 
        null, 
        [
            "m.movie_name",
            "m.rating",
            "m.image_url"
        ], 
        null,
        "RAND()",
        6
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