<?php 

class TaskController {
    
    private $gateway;
    public function __construct(TaskGateway $gateway, private int $user_id) {
        $this->gateway = $gateway;
    }
    
    public function processRequest(string $method, ?string $id ): void {

        if ( $id === null ) {
            if ( $method == "GET" ) {
                echo json_encode( $this->gateway->getAllForUser( $this->user_id ) );
            } elseif ( $method == "POST" ) {
                $data = (array) json_decode(file_get_contents("php://input"), 1);
                // var_dump($data);
                $errors = $this->getValidationErrors( $data );
                if ( ! empty( $errors ) ) {
                    $this->respondUnprocessableEntity( $errors );
                    return;
                }
                $id = $this->gateway->create( $data );
                $this->respondCreated( $id );                
            } else {
                $this->respondMethodNotAllowed( "GET, POST" );
            }
        } else {
            $task = $this->gateway->get($this->user_id, $id );
            if ( ! $task ) {
                $this->respondNotFound( $id );
                return;
            }     
            switch( $method ) {
                case "GET":
                    echo json_encode( $this->gateway->get($this->user_id, $id ) );
                    break;
                case "DELETE":
                    $rows = $this->gateway->delete($this->user_id, $id );
                    echo json_encode(["message" => "Task deleted.", "rows" => $rows]);
                    break;
                case "PATCH":
                    $data = (array) json_decode(file_get_contents("php://input"), 1);
                    $errors = $this->getValidationErrors( $data, false );
                    
                    if ( ! empty( $errors ) ) {
                        $this->respondUnprocessableEntity( $errors );
                        return;
                    }
                    
                    $rows = $this->gateway->update($this->user_id, $id, $data );
                    echo json_encode(["rows" => $rows, "message" => "Task updated for ID: $id"]);
                    break;
                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
                    break;
            }       
        }
    }
    
    private function respondMethodNotAllowed( string $allow_methods ): void {
        http_response_code(405);
        header("Allow: $allow_methods");        
    }
    
    private function respondNotFound( string $id ): void {
        http_response_code(404);
        echo json_encode(["message" => "Task with ID $id not found"]);
    }
    
    private function respondCreated( string $id ): void {
        http_response_code(201);
        echo json_encode(["message" => "Task created", "id" => $id]);
    }    

    private function respondDeleted(): void {
        http_response_code(410);
        echo json_encode(["message" => "Task deleted"]);
    } 
    
    private function respondUnprocessableEntity( array $errors ): void {
        http_response_code(422);
        echo json_encode( ["errors" => $errors] );
    } 
    
    private function getValidationErrors( array $data, $is_new = true ): array {
        // var_dump( $data["priority"] );
        // var_dump( $is_new );
        $errors = [];
        if ( $is_new && ! isset( $data["name"] ) ) {
            $errors[] = "Name field is required.";
        }
        if ( $is_new &&  empty( $data["name"] )  ) {
            $errors[] = "Name field should not empty";
        }
        
        if ( array_key_exists("name", $data) && empty( $data["name"] ) ) {
            $errors[] = "Invalid task name";
        }
        
        if ( ! empty( $data["priority"] ) && ! is_int( $data["priority"] )  ) {
            $errors[] = "Prioirity field should be integer";
        }
        
        if ( array_key_exists("priority", $data) && empty( $data["priority"] ) ) {
            $errors[] = "Invalid priority value";
        }
        
        return $errors;
    }    

}