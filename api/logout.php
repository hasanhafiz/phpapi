<?php 
/**
 * Removing access token provide by endpoint
 */
declare(strict_types = 1);
require __DIR__ . "/bootstrap.php";

if ( $_SERVER["REQUEST_METHOD"]  != "POST" ) {
    http_response_code(405);
    header("Allow: POST");
    exit;
}

// get the input data 
$data = (array) json_decode(file_get_contents("php://input"), true);

if ( ! array_key_exists( "token", $data ) ) {
    http_response_code(400);     // 400 Bad Request
    echo json_encode([ "message" => "Missing token"]);
    exit;
}

$database = new Database( $_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"] );
$refresh_token_gateway = new RefreshTokenGateway( $database, $_ENV["SECRET_KEY"] );
$refresh_token_gateway->delete( $data["token"] );
