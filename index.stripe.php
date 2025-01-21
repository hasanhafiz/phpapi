<?php 

$ch = curl_init();
$api_key = "sk_test_51QifOnF87bBorRVpf9FaVSKSuJTvjBQUs6SAYA98P4efGDmOIkQeCRG8PaxKjZVn8xPok2vlzQR2uK53A99U5ylZ00220sBfbO";

$data = [
  "name" => "hafiz Stripe",
  "email" => "hafizstripe@gmail.com"
];

// create a new customer using SDK
// Libraries and tools for interacting with your Stripe integration.
// SDK stands for Software Development Kit. It's a collection of tools that help developers build software for a specific platform or operating system. 

require __DIR__ . "/vendor/autoload.php";

$stripe = new \Stripe\StripeClient( $api_key );
$customer = $stripe->customers->create( $data );
print_r( $customer );

/*
// create a new customer using curl
curl_setopt_array($ch,[
                //   CURLOPT_URL => "https://api.github.com/gists", // return all gists
                  CURLOPT_URL => "https://api.stripe.com/v1/customers", // get individual gists info
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_USERPWD => $api_key,
                  CURLOPT_POSTFIELDS => http_build_query( $data ),
]);

$response = curl_exec( $ch );
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo $status_code;

// echo $response;
$data = json_decode( $response );
print_r($data);
*/