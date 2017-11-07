<?php

require_once ($_SERVER['DOCUMENT_ROOT']."/wp-config.php");

$dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST;
$user = DB_USER;
$password = DB_PASSWORD;

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    throw new Exception($e->getMessage());
}

define('API_KEY', 'x3bqkbc8n7e7hcmnkcq9p2cpkzb364rg');