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

$payload = [
    "sub" => $user["id"],
    "name" => $user["name"] 
];

// $access_token = base64_encode( json_encode( $payload ) );
$access_token = (new JWTCodec($_ENV["SECRET_KEY"]))->encode( $payload );
// echo $access_token , "\n";

// echo base64_decode( $access_token ) , "\n";
echo json_encode(["access_token" => $access_token, "message" => "Successful authentication"]);

