<?php
$apiKey = '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';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/models');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Erro cURL: ' . curl_error($ch);
} else {
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Status HTTP: $statusCode\n";
    echo "Resposta: $response";
}

curl_close($ch);
?>
