<?php 

class ErrorHandler {
    
    public static function handleError(
        int $errorno,
        string $errostr,
        string $errorfile,
        int $errorline
    ) {
        throw new ErrorException( $errostr,0, $errorno,  $errorfile, $errorline );
    }
    
    public static function handleException(Throwable $exception) {
        http_response_code(500);
        echo json_encode([
            "code" => $exception->getCode(),
            "line" => $exception->getLine(),
            "file" => $exception->getFile(),
            "message" => $exception->getMessage()
        ]);
    }
}