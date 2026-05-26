<?php

require_once(__DIR__ . "/../../middleware/auth.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

$sql = "
SELECT
    id,
    nome_eixo,
    observacao
FROM eixo
ORDER BY nome_eixo ASC
";

$resultado = $conexao->query($sql);

$dados = [];

while ($linha = $resultado->fetch_assoc()) {
    $dados[] = $linha;
}

respostaJSON(
    true,
    "Eixos carregados.",
    $dados
);

?>