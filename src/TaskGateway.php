<?php 

class TaskGateway {
    private PDO $conn;
    public function __construct( Database $database ) {
        $this->conn = $database->getConnection();
    }
    
    public function getAll() {        
        $sql = "SELECT * FROM task ORDER BY name";
        $stmt = $this->conn->query( $sql ) ;
        
        // $row = $stmt->fetch( PDO::FETCH_ASSOC );
        // $data = [];
        // while ($row = $stmt->fetch( PDO::FETCH_ASSOC )) {
        //     $row['is_completed'] = (bool) $row['is_completed'];
        //     $data[] = $row;
        // }
        // return $data;
        return $stmt->fetchAll( PDO::FETCH_ASSOC );        
    }
    
    public function getAllForUser( int $user_id ): array {        
        $sql = "SELECT * FROM task WHERE user_id = :user_id ORDER BY name";
        $stmt = $this->conn->prepare( $sql ) ;
        
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // $row = $stmt->fetch( PDO::FETCH_ASSOC );
        // $data = [];
        // while ($row = $stmt->fetch( PDO::FETCH_ASSOC )) {
        //     $row['is_completed'] = (bool) $row['is_completed'];
        //     $data[] = $row;
        // }
        // return $data;
        return $stmt->fetchAll( PDO::FETCH_ASSOC );        
    }    
    
    public function get(int $user_id, string $id ): array|false {
        $sql = "SELECT * FROM task WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare( $sql  );
        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();        
        $result = $stmt->fetch( PDO::FETCH_ASSOC );
        // var_dump( $result );
        return $result;
        // var_dump( $result );
    }
    
    public function create( array $data ): int {
        $sql = "INSERT INTO task (name, priority, is_completed)
                            VALUES(:name, :priority, :is_completed) ";
        $stmt = $this->conn->prepare( $sql  );
        
        if ( empty( $data["priority"] ) ) {
            $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_INT);
        }
        
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);        
        $stmt->bindValue(":is_completed", $data["is_completed"] ?? false, PDO::PARAM_BOOL);
        
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    
    public function delete(int $user_id, string $id ): int {
        $sql = "DELETE FROM task WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare( $sql  );        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);                
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);                
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    public function update(int $user_id, string $id, array $data ) {
        $sets = array_map( function($value){
            return "$value = :$value";
        }, array_keys( $data ));
        
        $sql = "UPDATE task SET " . implode(", ", $sets) . " WHERE id = :id AND user_id = :user_id"; 
        $stmt = $this->conn->prepare( $sql );
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        
        foreach( $data as $name => $value ) {
            $stmt->bindValue(":$name", $value);
        }
        
        $stmt->execute();
        return $stmt->rowCount();
    }    
}