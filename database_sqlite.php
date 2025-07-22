<?php
// database_sqlite.php - Classe para gerenciamento do banco de dados SQLite

require_once 'config.php';
require_once 'logger.php';

class Database {
    private $pdo;
    private static $instance = null;
    
    private function __construct() {
        try {
            $dbPath = __DIR__ . '/sistema.db';
            $this->pdo = new PDO("sqlite:$dbPath", null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            Logger::error("Erro de conexão com banco de dados: " . $e->getMessage());
            throw new Exception("Erro de conexão com banco de dados");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    // Métodos para operações com convênios
    public function listarConvenios($segmento = 'consignado') {
        $sql = "SELECT * FROM convenios WHERE segmento = ? ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$segmento]);
        return $stmt->fetchAll();
    }
    
    public function buscarConvenioPorNome($nome, $segmento = 'consignado') {
        $sql = "SELECT * FROM convenios WHERE nome LIKE ? AND segmento = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["%$nome%", $segmento]);
        return $stmt->fetch();
    }
    
    public function cadastrarConvenio($dados) {
        $sql = "INSERT INTO convenios (nome, margem, segmento, ativo, created_at) VALUES (?, ?, ?, 1, datetime('now'))";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$dados['nome'], $dados['margem'], $dados['segmento']]);
    }
    
    public function atualizarConvenio($id, $dados) {
        $sql = "UPDATE convenios SET nome = ?, margem = ?, updated_at = datetime('now') WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$dados['nome'], $dados['margem'], $id]);
    }
    
    // Métodos para operações com produtos
    public function listarProdutos($convenio_id = null, $segmento = 'consignado') {
        if ($convenio_id) {
            $sql = "SELECT p.*, c.nome as convenio_nome FROM produtos p 
                    JOIN convenios c ON p.convenio_id = c.id 
                    WHERE p.convenio_id = ? AND c.segmento = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$convenio_id, $segmento]);
        } else {
            $sql = "SELECT p.*, c.nome as convenio_nome FROM produtos p 
                    JOIN convenios c ON p.convenio_id = c.id 
                    WHERE c.segmento = ? ORDER BY p.nome";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$segmento]);
        }
        return $stmt->fetchAll();
    }
    
    public function cadastrarProduto($dados) {
        $sql = "INSERT INTO produtos (nome, prazo, taxa, convenio_id, ativo, created_at) VALUES (?, ?, ?, ?, 1, datetime('now'))";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$dados['nome'], $dados['prazo'], $dados['taxa'], $dados['convenio_id']]);
    }
    
    // Métodos para logs de interação
    public function logInteracao($usuario_id, $mensagem, $resposta, $intencao, $parametros) {
        $sql = "INSERT INTO logs_interacao (usuario_id, mensagem, resposta, intencao, parametros, created_at) 
                VALUES (?, ?, ?, ?, ?, datetime('now'))";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $usuario_id, 
            $mensagem, 
            $resposta, 
            $intencao, 
            json_encode($parametros)
        ]);
    }
    
    // Método para criar as tabelas necessárias
    public function criarTabelas() {
        $sqls = [
            "CREATE TABLE IF NOT EXISTS convenios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                margem REAL NOT NULL,
                segmento TEXT DEFAULT 'consignado',
                ativo INTEGER DEFAULT 1,
                created_at TEXT DEFAULT (datetime('now')),
                updated_at TEXT DEFAULT (datetime('now'))
            )",
            
            "CREATE TABLE IF NOT EXISTS produtos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                prazo INTEGER NOT NULL,
                taxa REAL DEFAULT NULL,
                convenio_id INTEGER NOT NULL,
                ativo INTEGER DEFAULT 1,
                created_at TEXT DEFAULT (datetime('now')),
                updated_at TEXT DEFAULT (datetime('now')),
                FOREIGN KEY (convenio_id) REFERENCES convenios(id)
            )",
            
            "CREATE TABLE IF NOT EXISTS logs_interacao (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER DEFAULT NULL,
                mensagem TEXT NOT NULL,
                resposta TEXT NOT NULL,
                intencao TEXT DEFAULT NULL,
                parametros TEXT DEFAULT NULL,
                created_at TEXT DEFAULT (datetime('now'))
            )",
            
            "CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                segmento TEXT DEFAULT 'consignado',
                permissoes TEXT DEFAULT NULL,
                ativo INTEGER DEFAULT 1,
                created_at TEXT DEFAULT (datetime('now'))
            )"
        ];
        
        foreach ($sqls as $sql) {
            $this->pdo->exec($sql);
        }
        
        // Inserir dados de exemplo se não existirem
        $this->inserirDadosExemplo();
    }
    
    private function inserirDadosExemplo() {
        // Verificar se já existem dados
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM convenios");
        if ($stmt->fetchColumn() > 0) {
            return; // Dados já existem
        }
        
        // Inserir convênios de exemplo
        $convenios = [
            ['INSS', 35.00, 'consignado'],
            ['SIAPE', 30.00, 'consignado'],
            ['Prefeitura Municipal', 25.00, 'consignado'],
            ['Seguradora Alpha', 0.00, 'protecao_veicular'],
            ['Construtora Beta', 0.00, 'construtora'],
            ['Administradora Gamma', 0.00, 'condominio']
        ];
        
        foreach ($convenios as $convenio) {
            $this->cadastrarConvenio([
                'nome' => $convenio[0],
                'margem' => $convenio[1],
                'segmento' => $convenio[2]
            ]);
        }
        
        // Inserir produtos de exemplo
        $produtos = [
            ['Empréstimo Novo', 84, 2.5, 1],
            ['Refinanciamento', 72, 2.3, 1],
            ['Cartão Consignado', 0, 0.0, 2],
            ['Empréstimo Servidor', 96, 2.1, 2]
        ];
        
        foreach ($produtos as $produto) {
            $this->cadastrarProduto([
                'nome' => $produto[0],
                'prazo' => $produto[1],
                'taxa' => $produto[2],
                'convenio_id' => $produto[3]
            ]);
        }
    }
}
?>

