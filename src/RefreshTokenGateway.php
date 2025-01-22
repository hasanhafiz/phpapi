<?php 

class RefreshTokenGateway {
    private PDO $conn;
    private string $secret_key;
        
    public function __construct( Database $database, string $secret_key ) {
        $this->conn = $database->getConnection();
        $this->secret_key = $secret_key;
    }
    
    public function create( string $token, int $expiray ): bool {
        $sql = "INSERT INTO refresh_token (token_hash, expires_at) VALUES ( :token_hash, :expires_at )";
        $stmt = $this->conn->prepare( $sql );
        
        // echo hash_hmac('sha256', 'The quick brown fox jumped over the lazy dog.', 'secret'); 
        // output: 9c5c42422b03f0ee32949920649445e417b2c634050833c5165704b825c2a53b 
        $token_hash = hash_hmac('sha256', $token, $this->secret_key ); 
        
        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);
        $stmt->bindValue(":expires_at", $expiray, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function delete( string $token ): int {
        $sql = "DELETE FROM refresh_token WHERE token_hash = :token_hash";
        $stmt = $this->conn->prepare( $sql );
        
        $token_hash = hash_hmac('sha256', $token, $this->secret_key );         
        // echo hash_hmac('sha256', 'The quick brown fox jumped over the lazy dog.', 'secret'); 
        // output: 9c5c42422b03f0ee32949920649445e417b2c634050833c5165704b825c2a53b                
        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);        
        
        $stmt->execute();
        return $stmt->rowCount(); // return number of deleted rows
    }
    
    public function getByToken( string $token ): array | false {
        $sql = "SELECT * FROM refresh_token WHERE token_hash = :token_hash";
        $stmt = $this->conn->prepare( $sql );
        
        $token_hash = hash_hmac('sha256', $token, $this->secret_key );         
        // echo hash_hmac('sha256', 'The quick brown fox jumped over the lazy dog.', 'secret'); 
        // output: 9c5c42422b03f0ee32949920649445e417b2c634050833c5165704b825c2a53b                
        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);        
        
        $stmt->execute();
        return $stmt->fetch( PDO::FETCH_ASSOC ); // return number of deleted rows
    }    
}
