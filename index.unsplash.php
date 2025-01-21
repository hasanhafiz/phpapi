<?php 

$headers = [
    "Authorization: Client-ID ksAKMsPya9Oc4ZLj_44qK5IKVytu7rft6MX-2rsZ2kQ"
];

$response_headers = [];
$header_callback = function($ch, $header) use( &$response_headers ){
    // print_r( $header );
    $len = strlen($header);
    $parts = explode( ":", $header, 2 );
    
    if ( count( $parts ) <2 ) {
        return $len;
    }
    
    $response_headers[ $parts[0] ] = trim( $parts[1] );
    
    return $len;
};

// https://randomuser.me/apix
$ch = curl_init();
curl_setopt_array($ch,[
                  CURLOPT_URL => "https://api.unsplash.com/photos/random",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_HTTPHEADER => $headers,
                  CURLOPT_HEADERFUNCTION => $header_callback
]);

$response = curl_exec( $ch );

$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// $content_type = curl_getinfo( $ch, CURLINFO_CONTENT_TYPE );
// $content_length = curl_getinfo( $ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD );

curl_close($ch);

echo "STATUS CODE=", $status_code, "\n";
print_r( $response_headers );
echo $response;
// var_dump(json_decode( $response ));
?>
