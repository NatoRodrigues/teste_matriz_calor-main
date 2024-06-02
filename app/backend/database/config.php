<?php

// Obtenha a URL do JawsDB a partir das variáveis de ambiente
$url = parse_url(getenv('JAWSDB_URL'));

$servername = $url['sh4ob67ph9l80v61.cbetxkdyhwsb.us-east-1.rds.amazonaws.com'];
$username = $url['ni26ybmxp72pcyeh'];
$password = $url['r4lrxmphrn44ftyr'];
$database = substr($url['h138o4z12q7moerr'], 1);
$port = $url['3306'];

// Crie a conexão
$conn = new mysqli($servername, $username, $password, $database, $port);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Defina o charset
$conn->set_charset("utf8");

// Se a conexão for bem-sucedida, você pode prosseguir com suas operações
?>
