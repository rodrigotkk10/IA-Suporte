<?php
// ia_processor.php - Classe para processamento de IA

require_once 'config.php';
require_once 'logger.php';

class IAProcessor {
    private $segmento;
    
    public function __construct($segmento = 'consignado') {
        $this->segmento = $segmento;
    }
    
    public function processarMensagem($mensagem, $usuario_id = null) {
        try {
            Logger::info("Processando mensagem da IA", [
                'mensagem' => $mensagem,
                'segmento' => $this->segmento,
                'usuario_id' => $usuario_id
            ]);
            
            // Construir o prompt do sistema baseado no segmento
            $systemPrompt = $this->construirSystemPrompt();
            
            // Fazer requisição para a API da IA
            $respostaIA = $this->chamarAPIIA($systemPrompt, $mensagem);
            
            // Processar a resposta estruturada da IA
            $resultado = $this->processarRespostaIA($respostaIA);
            
            // Executar a ação se necessário
            if (isset($resultado['intencao']) && $resultado['intencao'] !== 'conversa') {
                $resultado = $this->executarAcao($resultado);
            }
            
            // Log da interação
            Logger::logInteracaoIA(
                $usuario_id,
                $mensagem,
                $resultado['resposta'],
                $resultado['intencao'] ?? 'conversa',
                $resultado['parametros'] ?? [],
                $resultado['sucesso'] ?? true
            );
            
            return $resultado;
            
        } catch (Exception $e) {
            Logger::error("Erro no processamento da IA: " . $e->getMessage());
            return [
                'resposta' => 'Desculpe, ocorreu um erro interno. Tente novamente em alguns instantes.',
                'sucesso' => false,
                'erro' => $e->getMessage()
            ];
        }
    }
    
    private function construirSystemPrompt() {
        $prompts = [
            'consignado' => "Você é um assistente especializado em sistemas de empréstimo consignado. 
                Você pode ajudar com:
                - Listar, cadastrar e atualizar convênios (INSS, SIAPE, Prefeituras, etc.)
                - Gerenciar produtos (Empréstimo Novo, Refinanciamento, Cartão Consignado)
                - Consultar informações sobre contratos e margens
                - Orientar sobre procedimentos do sistema
                
                IMPORTANTE: Sempre responda em formato JSON com a seguinte estrutura:
                {
                    \"intencao\": \"listar_convenios|cadastrar_convenio|atualizar_convenio|listar_produtos|cadastrar_produto|consultar_info|conversa\",
                    \"parametros\": {objeto com parâmetros necessários},
                    \"resposta_usuario\": \"mensagem amigável para o usuário\",
                    \"necessita_confirmacao\": true/false
                }
                
                Exemplos de intenções:
                - \"listar convênios\" -> {\"intencao\": \"listar_convenios\", \"parametros\": {}, \"resposta_usuario\": \"Vou listar os convênios cadastrados.\"}
                - \"cadastrar convênio INSS com margem 35%\" -> {\"intencao\": \"cadastrar_convenio\", \"parametros\": {\"nome\": \"INSS\", \"margem\": 35}, \"resposta_usuario\": \"Vou cadastrar o convênio INSS com margem de 35%.\"}",
                
            'protecao_veicular' => "Você é um assistente especializado em sistemas de proteção veicular.
                Você pode ajudar com gestão de apólices, sinistros, veículos e associados.
                Responda sempre em formato JSON estruturado.",
                
            'construtora' => "Você é um assistente especializado em sistemas para construtoras.
                Você pode ajudar com gestão de obras, clientes, contratos e financeiro.
                Responda sempre em formato JSON estruturado.",
                
            'condominio' => "Você é um assistente especializado em administração de condomínios.
                Você pode ajudar com gestão de moradores, financeiro, manutenção e assembleia.
                Responda sempre em formato JSON estruturado."
        ];
        
        return $prompts[$this->segmento] ?? $prompts['consignado'];
    }
    
    private function chamarAPIIA($systemPrompt, $mensagem) {
        $payload = [
            'model' => 'gpt-4.1-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $mensagem]
            ],
            'temperature' => 0.3,
            'max_tokens' => 1000
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
        
        if ($httpCode !== 200) {
            throw new Exception("Erro na API da IA. HTTP Code: $httpCode");
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new Exception("Resposta inválida da API da IA");
        }
        
        return $result['choices'][0]['message']['content'];
    }
    
    private function processarRespostaIA($respostaIA) {
        // Tentar decodificar JSON da resposta da IA
        $dados = json_decode($respostaIA, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Se não for JSON válido, tratar como conversa normal
            return [
                'intencao' => 'conversa',
                'resposta' => $respostaIA,
                'sucesso' => true
            ];
        }
        
        // Validar estrutura esperada
        if (!isset($dados['intencao']) || !isset($dados['resposta_usuario'])) {
            return [
                'intencao' => 'conversa',
                'resposta' => $dados['resposta_usuario'] ?? $respostaIA,
                'sucesso' => true
            ];
        }
        
        return [
            'intencao' => $dados['intencao'],
            'parametros' => $dados['parametros'] ?? [],
            'resposta' => $dados['resposta_usuario'],
            'necessita_confirmacao' => $dados['necessita_confirmacao'] ?? false,
            'sucesso' => true
        ];
    }
    
    private function executarAcao($dados) {
        $db = Database::getInstance();
        
        try {
            switch ($dados['intencao']) {
                case 'listar_convenios':
                    $convenios = $db->listarConvenios($this->segmento);
                    $lista = [];
                    foreach ($convenios as $convenio) {
                        $lista[] = $convenio['nome'] . " (Margem: " . $convenio['margem'] . "%)";
                    }
                    $dados['resposta'] = "Convênios cadastrados:\n" . implode("\n", $lista);
                    break;
                    
                case 'cadastrar_convenio':
                    $parametros = $dados['parametros'];
                    if (!isset($parametros['nome']) || !isset($parametros['margem'])) {
                        throw new Exception("Parâmetros insuficientes para cadastrar convênio");
                    }
                    
                    $dadosConvenio = [
                        'nome' => $parametros['nome'],
                        'margem' => floatval($parametros['margem']),
                        'segmento' => $this->segmento
                    ];
                    
                    if ($db->cadastrarConvenio($dadosConvenio)) {
                        $dados['resposta'] = "Convênio '{$parametros['nome']}' cadastrado com sucesso!";
                    } else {
                        throw new Exception("Erro ao cadastrar convênio");
                    }
                    break;
                    
                case 'listar_produtos':
                    $convenio_id = $dados['parametros']['convenio_id'] ?? null;
                    $produtos = $db->listarProdutos($convenio_id, $this->segmento);
                    
                    if (empty($produtos)) {
                        $dados['resposta'] = "Nenhum produto encontrado.";
                    } else {
                        $lista = [];
                        foreach ($produtos as $produto) {
                            $lista[] = $produto['nome'] . " - Prazo: " . $produto['prazo'] . " meses (Convênio: " . $produto['convenio_nome'] . ")";
                        }
                        $dados['resposta'] = "Produtos disponíveis:\n" . implode("\n", $lista);
                    }
                    break;
                    
                case 'cadastrar_produto':
                    $parametros = $dados['parametros'];
                    if (!isset($parametros['nome']) || !isset($parametros['prazo']) || !isset($parametros['convenio_id'])) {
                        throw new Exception("Parâmetros insuficientes para cadastrar produto");
                    }
                    
                    $dadosProduto = [
                        'nome' => $parametros['nome'],
                        'prazo' => intval($parametros['prazo']),
                        'taxa' => floatval($parametros['taxa'] ?? 0),
                        'convenio_id' => intval($parametros['convenio_id'])
                    ];
                    
                    if ($db->cadastrarProduto($dadosProduto)) {
                        $dados['resposta'] = "Produto '{$parametros['nome']}' cadastrado com sucesso!";
                    } else {
                        throw new Exception("Erro ao cadastrar produto");
                    }
                    break;
                    
                default:
                    // Intenção não reconhecida, manter como conversa
                    break;
            }
            
        } catch (Exception $e) {
            Logger::error("Erro na execução da ação: " . $e->getMessage());
            $dados['resposta'] = "Erro ao executar a operação: " . $e->getMessage();
            $dados['sucesso'] = false;
        }
        
        return $dados;
    }
}
?>

