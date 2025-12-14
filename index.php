<?php
require_once 'config/constants.php';
require_once APP_PATH . '/helpers/functions.php';

spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/core/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$request_uri = $_SERVER['REQUEST_URI'];
$query_string = $_SERVER['QUERY_STRING'] ?? '';
$uri = str_replace('?' . $query_string, '', $request_uri);
$uri = trim($uri, '/');

if (empty($_SESSION['csrf_token'])) {
    generateCsrfToken();
}

switch ($uri) {
    case '':
    case 'tasks':
        $controller = new TaskController();
        $controller->index();
        break;
        
    case 'tasks/create':
        $controller = new TaskController();
        $controller->create();
        break;
        
    case 'tasks/store':
        $controller = new TaskController();
        $controller->store();
        break;
        
    case preg_match('/^tasks\/edit\/(\d+)$/', $uri, $matches) ? true : false:
        $controller = new TaskController();
        $controller->edit($matches[1]);
        break;
        
    case preg_match('/^tasks\/update\/(\d+)$/', $uri, $matches) ? true : false:
        $controller = new TaskController();
        $controller->update($matches[1]);
        break;
        
    case preg_match('/^tasks\/delete\/(\d+)$/', $uri, $matches) ? true : false:
        $controller = new TaskController();
        $controller->delete($matches[1]);
        break;
        
    case 'login':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'register':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;
        
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
        
    default:
        http_response_code(404);
        echo "Страница не найдена";
        break;
}