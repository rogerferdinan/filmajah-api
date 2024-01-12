<?php
    header('Content-Type: application/json; charset=utf-8');
    $post = json_decode(file_get_contents('php://input'), true);
    $email = (isset($post) && array_key_exists("email", $post)) ? $post["email"] : "";
    $movie_name = (isset($post) && array_key_exists("movie_name", $post)) ? $post["movie_name"] : "";

    if($email === "") {
        echo json_encode([
            "is_success" => False,
            "message" => "Email cannot be empty"
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
    
    // Sanitize
    $email = $GLOBALS["db"]->sanitize($email);
    $movie_name = $GLOBALS["db"]->sanitize($movie_name);

    ['result'=>$result,'error'=>$error] = $GLOBALS["db"]->select(
        "movies m",
        [
            "JOIN producers p" => "p.producer_id = m.producer_id"
        ], [
            "m.movie_id",
            "m.movie_name",
            "m.movie_length",
            "YEAR(m.release_date) as release_year",
            "m.rating",
            "m.image_url",
            "p.producer_name",
            "m.synopsis",
        ], [
            "movie_name = '$movie_name'"
        ]
    );

    $row = $result->fetch_array();
    $movie_id = $row["movie_id"];
    
    // Get collection status
    ['result'=>$collection_query,'error'=>$error] = $GLOBALS["db"]->select(
        "movies m",
        [
            "LEFT JOIN collection c" => "c.movie_id = m.movie_id"
        ], [
            "IF(COUNT(m.movie_id) > 0, true, false) add_collection"
        ], [
            "m.movie_id = '$movie_id'",
            "email = '$email'"
        ]
    );
    $collection_row = $collection_query->fetch_array();
    $is_added = $collection_row["add_collection"];
    // Get Movie Genres
    ['result'=>$genre_query,'error'=>$error] = $GLOBALS["db"]->select(
        "movie_genre mg",
        [
            "JOIN genres g"=>"mg.genre_id=g.genre_id"
        ], [
            "g.genre"
        ], [
            "movie_id='$movie_id'"
        ]
    );
    while($genre_row = $genre_query->fetch_array()) {
        $genre[] = $genre_row["genre"];
    }
    // Get Movie Casters
    ['result'=>$cast_query,'error'=>$error] = $GLOBALS["db"]->select(
        "movie_casts mc",
        [
            "JOIN casts c"=> "c.cast_id=mc.cast_id"
        ], [
            "c.cast_name"
        ], [
            "movie_id='$movie_id'"
        ]
    );
    while($cast_row = $cast_query->fetch_array()) {
        $cast[] = $cast_row["cast_name"];
    }
    
    $data = array();
    $data = [
        "movie_name" => $row["movie_name"],
        "length" => (int)$row["movie_length"] ?? null,
        "release_year" => (int)$row["release_year"] ?? null,
        "producer_name" => $row["producer_name"] ?? null,
        "casts" => $cast ?? null,
        "genres" => $genre ?? null,
        "synopsis" => $row["synopsis"] ?? null,
        "rating" => (float)$row["rating"] ?? null,
        "image_url" => $row["image_url"] ?? null,
        "is_added" => (bool)$is_added
    ];
    echo json_encode($data);
?>