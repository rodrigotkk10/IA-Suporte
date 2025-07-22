<?php
// config.php - Configurações do sistema

// Configurações da API OpenAI
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: 'SUA_CHAVE_AQUI');
define('OPENAI_API_BASE', getenv('OPENAI_API_BASE') ?: 'https://api.openai.com/v1');

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_consignado');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configurações de segurança
define('SESSION_TIMEOUT', 3600); // 1 hora
define('MAX_REQUESTS_PER_MINUTE', 60);

// Configurações de logs
define('LOG_FILE', __DIR__ . '/logs/sistema.log');
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR

// Segmentos de clientes suportados
define('SEGMENTOS', [
    'consignado' => 'Empréstimo Consignado',
    'protecao_veicular' => 'Proteção Veicular', 
    'construtora' => 'Construtora',
    'condominio' => 'Administração de Condomínios'
]);

// Criar diretório de logs se não existir
if (!file_exists(dirname(LOG_FILE))) {
    mkdir(dirname(LOG_FILE), 0755, true);
}
?>

