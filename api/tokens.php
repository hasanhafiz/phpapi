<?php 
declare( strict_types = 1 );

$payload = [
    "sub" => $user["id"],
    "name" => $user["name"],
    "exp" => time() + 300
];

$refresh_token_expiry = time() + 5*24*60*60 ; // 5 days
$access_token = $codec->encode( $payload );
$refresh_token = $codec->encode([
    "sub" => $user["id"],
    "exp" => $refresh_token_expiry 
]);

echo json_encode([
    "access_token" => $access_token, 
    "refresh_token" => $refresh_token, 
]);