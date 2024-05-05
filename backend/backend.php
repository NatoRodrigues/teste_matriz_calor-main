<?php
// Incluir o arquivo de conexão com o banco de dados
include './database/config.php';

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
