<?php
// public/index.php

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/helpers.php';

$controllerName = strtolower($_GET['c'] ?? 'home');
$actionName     = $_GET['a'] ?? 'index';

$controllerClass = ucfirst($controllerName) . 'Controller';
$controllerFile  = APP_PATH . '/controllers/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo "Controller not found";
    exit;
}

require_once $controllerFile;

if (!class_exists($controllerClass)) {
    http_response_code(500);
    echo "Controller class not found";
    exit;
}

$controller = new $controllerClass();

if (!method_exists($controller, $actionName)) {
    http_response_code(404);
    echo "Action not found";
    exit;
}

$controller->$actionName();
