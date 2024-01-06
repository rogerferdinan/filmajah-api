<?php

$router = new Router();
$router->get("/", "/controller/home.php");
$router->get("/api/recommendation", "/controller/recommendation.php");
$router->get("/api/trending", "/controller/recommendation.php");

$router->post("/api/login", "/controller/login.php");
$router->post("/api/signup", "/controller/signup.php");
$router->post("/api/get_collection", "/controller/get_collection.php");
$router->post("/api/add_collection", "/controller/add_collection.php");
$router->post("/api/movie_info", "/controller/get_movie_info.php");
$router->post("/api/get_profile", "/controller/get_profile.php");

// route the user request
$router->route($_SERVER["REQUEST_METHOD"], parse_url($_SERVER["REQUEST_URI"])["path"]);
?>