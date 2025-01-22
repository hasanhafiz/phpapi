<?php 

declare(strict_types = 1);
require __DIR__ . "/api/bootstrap.php";

$database = new Database( $_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"] );
$refresh_token_gateway = new RefreshTokenGateway( $database, $_ENV["SECRET_KEY"] );
echo $refresh_token_gateway->deleteExpiredTokens() ,  "\n";