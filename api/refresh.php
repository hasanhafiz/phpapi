<?php 

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

$codec = new JWTCodec( $_ENV["SECRET_KEY"] );
try {
    $payload = $codec->decode( $data["token"] );
} catch (Exception) {
    http_response_code(400);     // 400 Bad Request
    echo json_encode([ "message" => "Invalid token"]);
    exit;    
}

$user_id = $payload["sub"];

// before gettng user info by id, check refresh token exists or not.
// if exists, then invalid user
// Show error message
$database = new Database( $_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"] );
$refresh_token_gateway = new RefreshTokenGateway( $database, $_ENV["SECRET_KEY"] );
$refresh_token = $refresh_token_gateway->getByToken( $data["token"] );
if ( $refresh_token === false ) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid token (not on whitelist)"]);
    exit;
}

// now get user info using refresh token
// check user exists or not in the database
$user_gateway = new UserGateway( $database );
$user = $user_gateway->getUserById($user_id);

if ( $user == false ) {
    http_response_code(401); 
    echo json_encode([ "message" => "Invalid authentication"]);
    exit;        
}

// generate new token based on user id
require __DIR__ . "/tokens.php";

// save new token & refresh token to database.
// delete record if exists so that only one record will be exists
$refresh_token_gateway = new RefreshTokenGateway( $database, $_ENV["SECRET_KEY"] );

// these two parameters are difined in 'tokens.php' file
$refresh_token_gateway->delete($data["token"]);
$token_create = $refresh_token_gateway->create( $refresh_token, $refresh_token_expiry ); 