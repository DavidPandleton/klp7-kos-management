<?php

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

use App\Helpers\Session;

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestUri = parse_url($requestUri, PHP_URL_PATH);
$requestUri = ltrim($requestUri, '/');
$url = !empty($requestUri) ? $requestUri : ($_GET['url'] ?? '');

$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'AuthController';
$defaultMethod = $controllerName === 'AuthController' ? 'login' : 'index';
$methodName = $url[1] ?? $defaultMethod;
$params = array_slice($url, 2);

$controllerClass = "App\\Controllers\\" . $controllerName;

if (!file_exists(__DIR__ . '/../src/Controllers/' . $controllerName . '.php')) {
    http_response_code(404);
    require_once __DIR__ . '/../views/errors/404.php';
    exit;
}

require_once __DIR__ . '/../src/Middleware/Auth.php';

$controller = new $controllerClass();

if (!method_exists($controller, $methodName)) {
    http_response_code(404);
    require_once __DIR__ . '/../views/errors/404.php';
    exit;
}

call_user_func_array([$controller, $methodName], $params);