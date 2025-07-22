<?php
// install.php - Script de instalação e configuração inicial

require_once 'config.php';
require_once 'database_sqlite.php';
require_once 'logger.php';

echo "<h1>Instalação do Sistema Assistente IA</h1>";

try {
    echo "<h2>1. Verificando configurações...</h2>";
    
    // Verificar se a chave da OpenAI está configurada
    if (OPENAI_API_KEY === 'SUA_CHAVE_AQUI') {
        echo "<p style='color: orange;'>⚠️ ATENÇÃO: Configure sua chave da OpenAI no arquivo config.php ou na variável de ambiente OPENAI_API_KEY</p>";
    } else {
        echo "<p style='color: green;'>✅ Chave da OpenAI configurada</p>";
    }
    
    echo "<h2>2. Criando estrutura do banco de dados...</h2>";
    
    // Inicializar banco de dados
    $db = Database::getInstance();
    $db->criarTabelas();
    
    echo "<p style='color: green;'>✅ Tabelas criadas com sucesso</p>";
    echo "<p style='color: green;'>✅ Dados de exemplo inseridos</p>";
    
    echo "<h2>3. Testando conexões...</h2>";
    
    // Testar conexão com banco
    $convenios = $db->listarConvenios();
    echo "<p style='color: green;'>✅ Conexão com banco de dados: OK (" . count($convenios) . " convênios encontrados)</p>";
    
    // Testar logs
    Logger::info("Sistema instalado com sucesso");
    echo "<p style='color: green;'>✅ Sistema de logs: OK</p>";
    
    echo "<h2>4. Instalação concluída!</h2>";
    echo "<p>O sistema está pronto para uso. Acesse <a href='index.html'>index.html</a> para começar.</p>";
    
    echo "<h3>Comandos de teste sugeridos:</h3>";
    echo "<ul>";
    echo "<li>listar convênios</li>";
    echo "<li>listar produtos</li>";
    echo "<li>cadastrar convênio TESTE com margem 30%</li>";
    echo "<li>quais produtos tem para INSS?</li>";
    echo "<li>ajuda</li>";
    echo "</ul>";
    
    echo "<h3>Segmentos disponíveis:</h3>";
    echo "<ul>";
    foreach (SEGMENTOS as $key => $nome) {
        echo "<li><strong>$key:</strong> $nome</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na instalação: " . $e->getMessage() . "</p>";
    Logger::error("Erro na instalação: " . $e->getMessage());
}
?>

