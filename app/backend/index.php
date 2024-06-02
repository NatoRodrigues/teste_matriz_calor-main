<?php
// Incluir o arquivo de conexão com o banco de dados
include 'database/config.php';

// Função para enviar resposta JSON
function sendJsonResponse($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Verificar se o método da requisição é POST e se existem os parâmetros 'pixels' e 'thermistor_temp'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pixels']) && isset($_POST['thermistor_temp'])) {
    // Obter os dados dos parâmetros 'pixels' e 'thermistor_temp'
    $pixelsData = $_POST['pixels'];
    $thermistorTemp = $_POST['thermistor_temp'];

    // Preparar a consulta SQL de inserção
    $sql = "INSERT INTO dados_amg8833 (temperature_pixels, thermistor_temp) VALUES ('$pixelsData', '$thermistorTemp')";

    // Executar a consulta SQL de inserção e verificar se foi bem-sucedida
    if ($conn->query($sql) === TRUE) {
        sendJsonResponse(array('success' => 'Dados inseridos com sucesso.'));
    } else {
        sendJsonResponse(array('error' => 'Erro na inserção: ' . $conn->error));
    }
} 
// Verificar se o método da requisição é GET
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Executar a consulta SQL para selecionar o último registro da coluna 'temperature_pixels' e 'thermistor_temp'
    $result = $conn->query("SELECT temperature_pixels, thermistor_temp FROM dados_amg8833 ORDER BY id DESC LIMIT 1");

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

            // Adicionar a temperatura do termistor ao array de dados
            $floatData['thermistor_temp'] = floatval($row['thermistor_temp']);

            // Codificar os dados em formato JSON
            sendJsonResponse($floatData);
        } else {
            // Se não houver linhas retornadas, exibir uma mensagem de erro
            sendJsonResponse(array('error' => 'Nenhuma linha retornada na consulta.'));
        }
    } else {
        // Se houver um erro na consulta, exibir uma mensagem de erro
        sendJsonResponse(array('error' => 'Erro na consulta: ' . $conn->error));
    }
} 
// Se o método não for POST ou GET, ou se os parâmetros estiverem faltando, exibir uma mensagem de erro
else {
    sendJsonResponse(array('error' => 'Método de requisição inválido ou parâmetros ausentes.'));
}
?>
