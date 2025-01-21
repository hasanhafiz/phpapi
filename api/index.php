<?php 
declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

ini_set("display_errors", "On");

// get the url
$path = $_SERVER["REQUEST_URI"];

// parst url and remove query string
$path = parse_url( $path, PHP_URL_PATH );

$parts = explode("/", $path);

 // items 2 i.e index 2 is resource

$resource = $parts[2];
$id = $parts[3] ?? null;

// get request method from server super global variable
$method = $_SERVER["REQUEST_METHOD"];

// check the url and if /tasks is not found, then generate 404 error
if ( $resource != 'tasks' ) {
    // header("{$_SERVER["SERVER_PROTOCOL"]} 404 Not found");
    http_response_code(404);
    exit;
}

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"],$_ENV["DB_USER"],$_ENV["DB_PASSWORD"]);
$user_gateway = new UserGateway( $database );

$auth = new Auth( $user_gateway );

// if ( ! $auth->authenticateAPIKey() ) {
//     exit;
// }

// instead of api key, we are not validation user by token
if ( ! $auth->authenticateAccessToken() ) {
    exit;
}

$user_id = $auth->getUserId();

// var_dump( $user_id );

// echo dirname(__DIR__); // returns parent directory
// require dirname(__DIR__) . "/src/TaskController.php";

$task_gateway = new TaskGateway( $database );

$controller = new TaskController( $task_gateway, $user_id );
$controller->processRequest( $method, $id );

// print_r($controller->getAll() );

// print_r($method);
// print_r($parts);