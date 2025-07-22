<?php
// logger.php - Classe para gerenciamento de logs

require_once 'config.php';

class Logger {
    private static function writeLog($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
        
        // Escrever no arquivo de log
        file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
        
        // Se for erro crítico, também escrever no error_log do PHP
        if ($level === 'ERROR') {
            error_log($message);
        }
    }
    
    public static function debug($message, $context = []) {
        if (LOG_LEVEL === 'DEBUG') {
            self::writeLog('DEBUG', $message, $context);
        }
    }
    
    public static function info($message, $context = []) {
        if (in_array(LOG_LEVEL, ['DEBUG', 'INFO'])) {
            self::writeLog('INFO', $message, $context);
        }
    }
    
    public static function warning($message, $context = []) {
        if (in_array(LOG_LEVEL, ['DEBUG', 'INFO', 'WARNING'])) {
            self::writeLog('WARNING', $message, $context);
        }
    }
    
    public static function error($message, $context = []) {
        self::writeLog('ERROR', $message, $context);
    }
    
    public static function logInteracaoIA($usuario_id, $mensagem, $resposta_ia, $intencao, $parametros, $sucesso) {
        $context = [
            'usuario_id' => $usuario_id,
            'mensagem' => $mensagem,
            'intencao' => $intencao,
            'parametros' => $parametros,
            'sucesso' => $sucesso
        ];
        
        $level = $sucesso ? 'INFO' : 'WARNING';
        $message = $sucesso ? 'Interação IA processada com sucesso' : 'Erro no processamento da interação IA';
        
        self::writeLog($level, $message, $context);
    }
}
?>

