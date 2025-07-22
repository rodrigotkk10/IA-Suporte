<?php
// teste_openai.php - teste simples da API OpenAI

// Defina sua chave API aqui (sem quebras ou espaços extras)
$apiKey = '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';

// Endpoint da API
$apiUrl = 'https://api.openai.com/v1/chat/completions';

// Payload básico para testar o chat
$data = [
    'model' => 'gpt-4.1-mini',
    'messages' => [
        ['role' => 'system', 'content' => 'Você é um assistente útil.'],
        ['role' => 'user', 'content' => 'Olá, teste de conexão!']
    ],
    'temperature' => 0.5,
    'max_tokens' => 50
];

// Configurar cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curlError) {
    echo "Erro cURL: $curlError";
} else {
    echo "Status HTTP: $httpCode\n";
    echo "Resposta da API:\n$response\n";
}
?>
