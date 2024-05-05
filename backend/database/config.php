<?php

$servername = "localhost";
$username = "root";
$password = NULL;
$database = "amg8833";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
else {
    
}

$conn->set_charset("utf8");

?>
