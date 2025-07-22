<?php
// db_mock.php

$convenios = [
  ['id' => 1, 'nome' => 'INSS', 'margem' => 35],
  ['id' => 2, 'nome' => 'SIAPE', 'margem' => 30],
  ['id' => 3, 'nome' => 'Prefeitura', 'margem' => 25],
];

$produtos = [
  ['id' => 1, 'nome' => 'Empréstimo Novo', 'prazo' => 84, 'convenio_id' => 1],
  ['id' => 2, 'nome' => 'Refinanciamento', 'prazo' => 72, 'convenio_id' => 2],
];

function listarConvenios() {
  global $convenios;
  return $convenios;
}

function buscarConvenioPorNome($nome) {
  global $convenios;
  foreach ($convenios as $c) {
    if (strtolower($c['nome']) === strtolower($nome)) {
      return $c;
    }
  }
  return null;
}

function listarProdutosDoConvenio($convenioId) {
  global $produtos;
  return array_filter($produtos, fn($p) => $p['convenio_id'] == $convenioId);
}
?>
