<?php 

$headers = [
    "Authorization: token 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"
];

$payload = json_encode( [
    "name" => "Created from API",
    "description" => "An example API-created repo"
]);

// https://randomuser.me/apix
$ch = curl_init();
curl_setopt_array($ch,[
                  CURLOPT_URL => "https://api.github.com/user/repos",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_HTTPHEADER => $headers,
                  CURLOPT_USERAGENT => "hasanhafiz",
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $payload
]);

$response = curl_exec( $ch );

$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// $content_type = curl_getinfo( $ch, CURLINFO_CONTENT_TYPE );
// $content_length = curl_getinfo( $ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD );

curl_close($ch);

// echo "STATUS CODE=", $status_code, "\n";

// echo $response;
print_r(json_decode( $response ));