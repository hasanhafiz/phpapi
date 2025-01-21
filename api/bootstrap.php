<?php 

require dirname(__DIR__) . "/vendor/autoload.php";

// Create customer handler to display message beautifully
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

// load database information to $_ENV superglobal variable
$dotenv = Dotenv\Dotenv::createImmutable( dirname(__DIR__) );
$dotenv->load();

header("Content-type: application/json; charset=UTF-8");