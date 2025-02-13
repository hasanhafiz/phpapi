<?php 

class Database {
    
    public ?PDO $conn = null;
    
    public function __construct(
        private string $host,
        private string $dbname,
        private string $user,
        private string $password
    )
    {
    
    }
    
    public function getConnection(): PDO {
        if ( $this->conn == null ) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=UTF8";
            $this->conn = new PDO( $dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ] );    
        }
        return $this->conn;
    }
}