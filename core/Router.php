<?php

class Router {
    protected $routes = array();

    public function get($endpoint, $controller) {
        $this->routes["GET".$endpoint] = $controller;
    }
    public function post($endpoint, $controller) {
        $this->routes["POST".$endpoint] = $controller;
    }
    public function delete($endpoint, $controller) {
        $this->routes["DELETE".$endpoint] = $controller;
    }

    public function route($method, $endpoint) {
        if(array_key_exists($method.$endpoint, $this->routes)) {
            require BASE_PATH . $this->routes[$method.$endpoint];
        } else {
            require BASE_PATH . "/error.php";
        }
    }
};

?>