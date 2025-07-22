<?php
// api.php - API principal do sistema
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Tratar requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';
require_once 'database_sqlite.php';
require_once 'logger.php';
require_once 'ia_processor.php';

try {
    // Inicializar sessão
    session_start();
    
    // Obter dados da requisição
    $data = json_decode(file_get_contents('php://input'), true);
    $message = $data['message'] ?? '';
    $segmento = $data['segmento'] ?? 'consignado';
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    
    if (empty($message)) {
        throw new Exception('Mensagem não fornecida');
    }
    
    // Validar segmento
    if (!array_key_exists($segmento, SEGMENTOS)) {
        $segmento = 'consignado';
    }
    
    // Inicializar banco de dados (criar tabelas se necessário)
    $db = Database::getInstance();
    $db->criarTabelas();
    
    // Processar mensagem com IA
    $processor = new IAProcessor($segmento);
    $resultado = $processor->processarMensagem($message, $usuario_id);
    
    // Log da interação no banco de dados
    if ($usuario_id) {
        $db->logInteracao(
            $usuario_id,
            $message,
            $resultado['resposta'],
            $resultado['intencao'] ?? 'conversa',
            $resultado['parametros'] ?? []
        );
    }
    
    // Retornar resposta
    echo json_encode([
        'reply' => $resultado['resposta'],
        'success' => $resultado['sucesso'] ?? true,
        'intencao' => $resultado['intencao'] ?? 'conversa',
        'necessita_confirmacao' => $resultado['necessita_confirmacao'] ?? false
    ]);
    
} catch (Exception $e) {
    Logger::error("Erro na API: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'reply' => 'Desculpe, ocorreu um erro interno. Tente novamente.',
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
