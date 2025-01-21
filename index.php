<?php 

// api call using guzzle

require __DIR__ . "/vendor/autoload.php";

$client = new GuzzleHttp\Client;
$response = $client->request("GET", "https://api.github.com/user/repos", [
    "headers" => [
        "Authorization" => "token enter_your_githubaccesstoken",
        "User-Agent" => "hasanhafiz"
    ]
]); 

echo $response->getStatusCode() , "\n";
echo $response->getHeader("content-type")[0], "\n";
echo substr( $response->getBody(), 0, 200 ), "....\n";
