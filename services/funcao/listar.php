<?php

require_once(__DIR__ . "/../../middleware/auth.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

$sql = "
SELECT
    id,
    nome_funcao,
    descricao
FROM funcao
ORDER BY nome_funcao ASC
";

$resultado = $conexao->query($sql);

$dados = [];

while ($linha = $resultado->fetch_assoc()) {
    $dados[] = $linha;
}

respostaJSON(
    true,
    "Funções carregadas.",
    $dados
);

?>