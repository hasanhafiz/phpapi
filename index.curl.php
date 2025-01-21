<?php 

$ch = curl_init();
curl_setopt_array($ch,[
                //   CURLOPT_URL => "https://api.github.com/gists", // return all gists
                  CURLOPT_URL => "https://api.github.com/gists/93a6a9a83df0c806ec2826a77f925e1a", // get individual gists info
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_USERAGENT => "hasanhafiz",
]);

$response = curl_exec( $ch );

$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// echo "STATUS CODE=", $status_code, "\n";

// echo $response;
$data = json_decode( $response );
print_r($data);
// foreach ($data as $gist) {
//     echo  $gist->id, "-", $gist->description , "\n";
// }