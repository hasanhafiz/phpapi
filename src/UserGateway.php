<?php 

class UserGateway {
    private PDO $conn;
    public function __construct( Database $database ) {
        $this->conn = $database->getConnection();
    }
    
    public function getByAPIKey( string $api_kay ): array|false {
        $sql = "SELECT * FROM user WHERE api_key = :api_key";
        $stmt = $this->conn->prepare( $sql );
        
        $stmt->bindValue(":api_key", $api_kay, PDO::PARAM_STR);
        
        $stmt->execute();
        return $stmt->fetch( PDO::FETCH_ASSOC );
    }
    
    public function getUserByName( string $username ): array|false {
        $sql = "SELECT * FROM user WHERE username = :username";
        $stmt = $this->conn->prepare( $sql );
        
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        
        $stmt->execute();
        return $stmt->fetch( PDO::FETCH_ASSOC );
    }    
    
}