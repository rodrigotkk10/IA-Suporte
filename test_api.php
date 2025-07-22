<?php
// test_api.php - Teste da API OpenAI

require_once 'config.php';

echo "<h1>Teste da API OpenAI</h1>";

echo "<p>Chave da API: " . (OPENAI_API_KEY !== 'SUA_CHAVE_AQUI' ? 'Configurada' : 'NÃO CONFIGURADA') . "</p>";
echo "<p>Base URL: " . OPENAI_API_BASE . "</p>";

// Teste simples da API
$payload = [
    'model' => 'gpt-4.1-mini',
    'messages' => [
        ['role' => 'user', 'content' => 'Diga apenas "teste ok"']
    ],
    'max_tokens' => 10
];

$ch = curl_init(OPENAI_API_BASE . '/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . OPENAI_API_KEY
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h2>Resultado do Teste:</h2>";
echo "<p>HTTP Code: $httpCode</p>";

if ($httpCode === 200) {
    $result = json_decode($response, true);
    echo "<p style='color: green;'>✅ API funcionando!</p>";
    echo "<p>Resposta: " . ($result['choices'][0]['message']['content'] ?? 'N/A') . "</p>";
} else {
    echo "<p style='color: red;'>❌ Erro na API</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
?>

