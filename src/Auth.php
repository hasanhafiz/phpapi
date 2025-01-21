<?php 

class Auth {
    
    private int $user_id;
    
    public function __construct(private UserGateway $user_gateway) {
    }
    
    public function authenticateAPIKey(): bool {
        
        // set 400 error if api key is not found
        if ( empty($_SERVER["HTTP_X_API_KEY"] ) ) {
            http_response_code(400);
            echo "API Key is missing in Header";
            return false;
        }
        
        $api_key = $_SERVER["HTTP_X_API_KEY"];
        $user = $this->user_gateway->getByAPIKey( $api_key );
        
        if ( $user === false ) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid API Key"]);
            return false;
        }
        
        $this->user_id = $user["id"];
        return true;
    }
    
    public function getUserId(): int {
        return $this->user_id;
    }
    
    public function authenticateAccessToken(): bool {
        // var_dump( $_SERVER["HTTP_AUTHORIZATION"] );
        
        if ( ! preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches) ) {
            http_response_code(400); // 400 Bad Request
            echo json_encode(["message" => "Incomplete authorization header"]);
            return false;
        }
        $plain_text = base64_decode( $matches[1], true );
        if ( $plain_text === false ) {
            http_response_code(400); // 400 Bad Request
            echo json_encode(["message" => "Invalid authorization header"]);
            return false;            
        }
        
        $data = json_decode( $plain_text, true );
        if ( $data === false ) {
            http_response_code(400); // 400 Bad Request
            echo json_encode(["message" => "Invalid JSON"]);
            return false;            
        } 
        
        $this->user_id = $data["id"];
        return true;
    }
}