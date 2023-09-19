<?php
// var_dump($_SERVER["REQUEST_URI"]);
// echo "<br>";
declare(strict_types=1);

spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: *");
$request =  $_SERVER["REQUEST_METHOD"];
if($request == "OPTIONS"){
    http_response_code(200);
    exit;
}
$parts = explode("/", $_SERVER["REQUEST_URI"]);
if(empty($parts)){
    echo "Nothing here";
    exit;
}
if(empty($parts[2])){
    http_response_code(404);
    exit;
}
switch($parts[2]){
    case "products":
        $id = $parts[3] ?? null;
        $database = new Database("localhost", "product_db", "root", "");
        $gateway = new ProductGateway($database);
        $controller = new ProductController($gateway);
        $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
        break;
    case "users":
        $database = new Database("localhost", "product_db", "root", "");
        $gateway = new UsersGateway($database);
        $controller = new UserController($gateway);
        if($parts[3]=="login"||$parts[3]=="Login"){
            $controller->processAuthentication($_SERVER["REQUEST_METHOD"]);
            break;
        }
        $id = $parts[3] ?? null;
        $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
        break;
    default:
        http_response_code(404);
        exit;
}
?>
















