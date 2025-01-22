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

// to authenticate a user, we need username and password field.
// check if both fields are exists on input data

// var_dump( $data );
// exit;

if ( ! array_key_exists( "username", $data ) || 
    ! array_key_exists( "password", $data ) ) {
    // 400 Bad Request
    http_response_code(400);
    echo json_encode(["message" => "Missing Login credentials"]);
    exit;
}

// now check user exists or not in the database
$database = new Database( $_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"] );
$user_gateway = new UserGateway( $database );
$user = $user_gateway->getUserByName( $data["username"] );

if ( $user === false ) {
    http_response_code(401); // 401 Unauthorized
    echo json_encode(["message" => "Invalid authentication"]);
    exit;
}

// var_dump( $data );
// var_dump( $user );

if ( ! password_verify( $data["password"], $user["password_hash"] ) ){
    http_response_code(401); // 401 Unauthorized
    echo json_encode(["message" => "Invalid authentication"]);
    exit;    
}

$codec = new JWTCodec($_ENV["SECRET_KEY"]);

require __DIR__ . "/tokens.php";

// save new token & refresh token to database
$refresh_token_gateway = new RefreshTokenGateway( $database, $_ENV["SECRET_KEY"] );
// these two parameters are difined in 'tokens.php' file
$token_create = $refresh_token_gateway->create( $refresh_token, $refresh_token_expiry ); 
// var_dump( $token_create );