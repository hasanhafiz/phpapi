<?php 

class JWTCodec {
    
    public function __construct(private string $secret_key) {
    }
    
    public function encode( array $payload ): string {
        $header = json_encode([
            "typ" => "JWT",
            "alg" => "HS256"
        ]);        
        $header = $this->base64urlEncode( $header );
        
        $payload = json_encode($payload);
        $payload = $this->base64urlEncode( $payload );
        
        // generate signature based on above header & payload
        $signature = hash_hmac( "sha256",
                                $header . "." . $payload,
                                $this->secret_key,
                                true );
        $signature = $this->base64urlEncode( $signature );
        // echo "signature created (base64urlEncode):", $signature , "\n"; 
        return $header . "." . $payload .  "." . $signature;
    }
    
    public function decode( string $token ): array {
        
        // var_dump( $token );
        
        // $match = preg_match( "/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)/", $token, $matches );
        // var_dump( $match );
        
        if ( preg_match( "/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)/", $token, $matches ) === 0 ){            
            throw new InvalidArgumentException("Invalid token format");
        }
        
        // print_r( $matches );
        
        $signature = hash_hmac( "sha256",
                                $matches["header"] . "." . $matches["payload"],
                                $this->secret_key,
                                true );
        $signature = $this->base64urlEncode( $signature );
                                
        $signature_from_token = $matches["signature"];
        

        
        if ( ! hash_equals( $signature, $signature_from_token ) ) {
            // var_dump( hash_equals( $signature, $signature_from_token ) );
            throw new InvalidSignatureException;
        }
        
        // echo "signature created (again):", $matches["signature"] , "\n"; 
        // echo "signature from token:", $signature_from_token , "\n";         
        
        $payload = json_decode( $this->base64urlDecode( $matches["payload"] ), true );  
        
        // echo "signature created (again):", $matches["signature"] , "\n"; 
        // echo "signature from token:", $signature_from_token , "\n";   
        // echo "payload: ";
        // print_r( $payload );

        if ( $payload["exp"] < time() ) {
            throw new TokenExpiredException;
        }
        
        return $payload;
    }
    
    private function base64urlEncode( string $text ): string {
        return str_replace(
            ["+", "/", "="],
            ["-", "_", ""],
            base64_encode( $text )
        );
    }
    
    private function base64urlDecode( string $text ): string {
        return str_replace(
            ["-", "_", ""],
            ["+", "/", "="],
            base64_decode( $text )
        );
    }    
}