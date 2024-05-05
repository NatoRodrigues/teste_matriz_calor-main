<?php
// Ler o corpo da solicitação POST
$postData = file_get_contents('php://input');

// Debug: Imprimir os dados recebidos para verificação
echo "Dados recebidos: \n";
echo $postData . "\n";

// Decodificar os dados JSON recebidos
$data = json_decode($postData, true);

// Debug: Imprimir o resultado da decodificação
echo "Resultado da decodificação: \n";
var_dump($data);

// Verificar se os dados foram decodificados corretamente
if ($data === null) {
    // Se os dados não puderem ser decodificados, retornar uma resposta de erro
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'Erro: JSON inválido.'));
} else {
    // Processar os dados recebidos
    // Aqui você pode realizar as operações necessárias com os dados recebidos do Arduino
    
    // Enviar uma resposta de sucesso
}