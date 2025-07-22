# 🤖 Assistente IA para Sistema de Gestão

Sistema inteligente integrado para automatizar tarefas rotineiras em diferentes segmentos de negócios: empréstimos consignados, proteção veicular, construtoras e administração de condomínios.

## 🚀 Funcionalidades

### Inteligência Artificial Integrada
- **Processamento de Linguagem Natural**: Compreende comandos em português
- **Múltiplos Segmentos**: Adaptável para diferentes áreas de negócio
- **Execução de Ações**: Traduz comandos em operações no sistema
- **Logs Inteligentes**: Registra todas as interações para auditoria

### Operações Automatizadas
- **Convênios**: Listar, cadastrar e atualizar convênios
- **Produtos**: Gerenciar produtos por convênio
- **Consultas**: Buscar informações específicas
- **Relatórios**: Gerar relatórios automáticos

### Interface Moderna
- **Chat Intuitivo**: Interface conversacional amigável
- **Seletor de Segmentos**: Troca fácil entre áreas de negócio
- **Sugestões Rápidas**: Botões para comandos comuns
- **Feedback Visual**: Indicadores de carregamento e status

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Extensões PHP: PDO, cURL, JSON
- Chave da API OpenAI

## 🔧 Instalação

### 1. Configuração Inicial
```bash
# Clone ou extraia os arquivos do sistema
# Configure o banco de dados no arquivo config.php
```

### 2. Configurar API OpenAI
```php
// No arquivo config.php, configure:
define('OPENAI_API_KEY', 'sua_chave_aqui');

// Ou configure a variável de ambiente:
export OPENAI_API_KEY="sua_chave_aqui"
```

### 3. Executar Instalação
```bash
# Inicie o servidor PHP
php -S localhost:8000

# Acesse o instalador
http://localhost:8000/install.php
```

### 4. Usar o Sistema
```bash
# Acesse a interface principal
http://localhost:8000/index.html
```

## 🎯 Comandos de Teste

### Empréstimo Consignado
- `listar convênios`
- `cadastrar convênio INSS com margem 35%`
- `listar produtos`
- `quais produtos tem para INSS?`
- `cadastrar produto Empréstimo Novo com prazo 84 meses para convênio 1`

### Proteção Veicular
- `listar apólices`
- `cadastrar novo sinistro`
- `consultar veículo por placa`

### Construtora
- `listar obras em andamento`
- `cadastrar novo cliente`
- `consultar contratos`

### Administração de Condomínios
- `listar moradores`
- `gerar boleto de condomínio`
- `agendar assembleia`

## 🏗️ Arquitetura

### Componentes Principais
- **Frontend**: HTML5, CSS3, JavaScript (Tailwind CSS)
- **Backend**: PHP com arquitetura modular
- **Banco de Dados**: MySQL com estrutura otimizada
- **IA**: Integração com OpenAI GPT-3.5-turbo
- **Logs**: Sistema completo de auditoria

### Estrutura de Arquivos
```
├── index.html          # Interface principal
├── api.php            # API principal
├── config.php         # Configurações
├── database.php       # Gerenciamento de banco
├── ia_processor.php   # Processamento de IA
├── logger.php         # Sistema de logs
├── install.php        # Script de instalação
├── db_mock.php        # Mock de dados (legado)
└── logs/              # Diretório de logs
```

## 🔒 Segurança

- **Validação de Entrada**: Todos os parâmetros são validados
- **Prepared Statements**: Proteção contra SQL Injection
- **Logs de Auditoria**: Rastreamento completo de ações
- **Controle de Acesso**: Sistema baseado em permissões
- **Rate Limiting**: Proteção contra abuso da API

## 📊 Logs e Monitoramento

### Tipos de Log
- **INFO**: Operações normais
- **WARNING**: Situações de atenção
- **ERROR**: Erros do sistema
- **DEBUG**: Informações detalhadas (desenvolvimento)

### Localização dos Logs
- **Arquivo**: `logs/sistema.log`
- **Banco**: Tabela `logs_interacao`
- **PHP Error Log**: Para erros críticos

## 🔧 Personalização

### Adicionar Novo Segmento
1. Edite `config.php` e adicione o segmento em `SEGMENTOS`
2. Crie o prompt específico em `ia_processor.php`
3. Implemente as funções específicas em `database.php`
4. Teste as operações

### Adicionar Nova Funcionalidade
1. Defina a nova intenção no prompt do sistema
2. Implemente a lógica em `ia_processor.php`
3. Adicione as operações de banco em `database.php`
4. Teste e documente

## 🐛 Solução de Problemas

### Erro de Conexão com IA
- Verifique se a chave da OpenAI está correta
- Confirme a conectividade com a internet
- Verifique os logs em `logs/sistema.log`

### Erro de Banco de Dados
- Confirme as credenciais em `config.php`
- Execute `install.php` novamente
- Verifique permissões do usuário MySQL

### Interface Não Carrega
- Verifique se o servidor PHP está rodando
- Confirme se todos os arquivos estão presentes
- Verifique o console do navegador

## 📈 Próximos Passos

- [ ] Implementar autenticação de usuários
- [ ] Adicionar mais operações por segmento
- [ ] Criar dashboard de analytics
- [ ] Implementar notificações em tempo real
- [ ] Adicionar suporte a arquivos/documentos
- [ ] Criar API REST completa

## 🤝 Contribuição

Para contribuir com o projeto:
1. Faça um fork do repositório
2. Crie uma branch para sua feature
3. Implemente e teste suas mudanças
4. Envie um pull request

## 📄 Licença

Este projeto está sob licença MIT. Veja o arquivo LICENSE para mais detalhes.

## 📞 Suporte

Para suporte técnico:
- Verifique os logs do sistema
- Consulte a documentação
- Abra uma issue no repositório
