<?php

session_start();

require __DIR__ . "/../vendor/autoload.php";

// Define the site root
define ('SITE_ROOT', realpath(dirname(__FILE__)));

// Database info    
$dbname = "binsta";
$dbhost = "127.0.0.1";
$dbuser = "bit_academy";
$dbpass = "bit_academy";

// Match URL pattern
$pattern  = "/\w+/";
$request = $_GET['q'] ?? '';
preg_match_all($pattern, $request, $matches);
$controller = $matches[0][0] ?? 'post';
$method = $matches[0][1] ?? 'feed';
$id = $_GET['id'] ?? '';

$controllerClass = 'App\Controllers\\' . ucfirst($controller) . 'Controller';

// Instantiate the correct controller
if (class_exists($controllerClass)) {
    $controllerInstance = new $controllerClass($dbname, $dbhost, $dbuser, $dbpass);
} else {
    error(404, 'Controller "' . $matches[0][0] . '" not found');
}

// Call correct method
if (method_exists($controllerInstance, $method)) {
    echo $controllerInstance->$method($id);
} else {
    error(404, 'Page not found: ' . $method);
}
