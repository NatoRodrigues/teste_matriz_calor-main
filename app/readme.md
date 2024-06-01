Projeto: Sistema de Monitoramento de Temperatura em Tempo Real com ESP32 e Sensor AMG8833
Descrição do Projeto

Este projeto utiliza a plataforma ESP32 e a IDE do Arduino para enviar solicitações GET do sensor AMG8833 para um servidor Apache PHP. O PHP é responsável por decodificar e tratar essa matriz 8x8 em JSON, enviando-a para o banco de dados. Posteriormente, os dados são recuperados pelo JavaScript, que os renderiza de forma assíncrona em um gráfico de matriz bidimensional, simulando uma câmera térmica/detector infravermelho em tempo real. Essa abordagem oferece uma visualização dinâmica e atualizada das informações capturadas pelo sensor, proporcionando uma representação visual precisa das variações de temperatura em um ambiente.
Estrutura do Projeto

bash

/meu_projeto
    /Arduino
        esp32_apache_link.ino
    /frontend
        index.html
        contato.html
        sobre.html
        /CSS
            style.css
        /JS
            js.js
        /ASSETS
            /IMAGES
    /backend
        backend.php
    /DATABASE
        database.sql
        config.php

Requisitos

    Hardware
        ESP32
        Sensor AMG8833

    Software
        IDE do Arduino
        Servidor Apache (XAMPP)
        PHP 7.x ou superior
        MySQL
        Git

Configuração do Backend
1. Configuração do Servidor Apache e MySQL

    Instale e configure o XAMPP para rodar Apache e MySQL.
    Crie um banco de dados no MySQL:

sql

CREATE DATABASE sensor_data;
USE sensor_data;

CREATE TABLE dados_amg8833 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperature_pixels TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

    Arquivo de Configuração do Banco de Dados

Crie o arquivo backend/DATABASE/config.php:

php

<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sensor_data');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

    Arquivo backend.php

Crie o arquivo backend/backend.php com o seguinte conteúdo:

php

<?php
// Incluir o arquivo de conexão com o banco de dados
include './DATABASE/config.php';

// Verificar se o método da requisição é POST ou GET e se existe o parâmetro 'data'
if (($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') && isset($_REQUEST['data'])) {
    // Obter os dados do parâmetro 'data'
    $data = $_REQUEST['data'];

    // Preparar a consulta SQL de inserção
    $sql = "INSERT INTO dados_amg8833 (temperature_pixels) VALUES ('$data')";

    // Executar a consulta SQL de inserção e verificar se foi bem-sucedida
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array('success' => 'Dados inseridos com sucesso.'));
    } else {
        echo json_encode(array('error' => 'Erro na inserção: ' . $conn->error));
    }
} else {
    // Executar a consulta SQL para selecionar o último registro da coluna 'temperature_pixels'
    $result = $conn->query("SELECT temperature_pixels FROM dados_amg8833 ORDER BY id DESC LIMIT 1");

    // Verificar se a consulta foi bem-sucedida
    if ($result) {
        // Fetch the first row
        $row = $result->fetch_assoc();

        // Verificar se a linha foi recuperada com sucesso
        if ($row) {
            // Remover o caractere '|' substituindo por ','
            $row['temperature_pixels'] = str_replace('|', ',', $row['temperature_pixels']);
            // Explodir a string em um array separado por vírgulas
            $dataArray = explode(',', $row['temperature_pixels']);
            // Converter cada elemento do array em float
            $floatData = array_map('floatval', $dataArray);

            // Codificar os dados em formato JSON
            header('Content-Type: application/json');
            echo json_encode($floatData);
        } else {
            // Se não houver linhas retornadas, exibir uma mensagem de erro
            echo json_encode(array('error' => 'Nenhuma linha retornada na consulta.'));
        }
    } else {
        // Se houver um erro na consulta, exibir uma mensagem de erro
        echo json_encode(array('error' => 'Erro na consulta: ' . $conn->error));
    }
}
?>

Configuração do Frontend

    Copie os arquivos HTML, CSS e JavaScript para o diretório do servidor Apache.
    Atualize os links no HTML para garantir que os arquivos CSS e JavaScript sejam referenciados corretamente.

Configuração do Arduino

    Abra o arquivo esp32_apache_link.ino na IDE do Arduino.
    Verifique se a placa selecionada é a ESP32.
    Carregue o código para a placa ESP32.

Conexão do Sensor

Conecte o sensor AMG8833 à placa ESP32 conforme as especificações do fabricante. Certifique-se de conectar os pinos corretamente e de fornecer a alimentação adequada.
Acesso ao Dashboard

Após configurar o ambiente e carregar o código para a placa ESP32, você pode acessar o dashboard em http://localhost/frontend/index.html para visualizar os dados de temperatura em tempo real.
Considerações Finais

Este projeto oferece uma solução completa para monitoramento de temperatura em tempo real, utilizando hardware, software e serviços em nuvem. Certifique-se de seguir todas as etapas de configuração e conexão para garantir o funcionamento adequado do sistema. Boa sorte! 🌡️🚀
