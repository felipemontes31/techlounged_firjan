<?php

require_once("../../config/conexao.php");


header("Content-Type: application/json");

$sql = "
SELECT

    ra.id,
    ra.data_execucao,
    ra.data_finalizacao,
    ra.tema_especifico,
    ra.status,
    ra.publico_previsto,
    ra.publico_realizado,
    ra.data_criacao,

    a.nome_projeto,

    e.nome_espaco,

    uc.nome AS criado_por_nome,
    uu.nome AS atualizado_por_nome

FROM registro_atividade ra

INNER JOIN atividade a
ON ra.id_atividade = a.id

INNER JOIN espaco e
ON ra.id_espaco = e.id

INNER JOIN usuario uc
ON ra.criado_por = uc.id

INNER JOIN usuario uu
ON ra.atualizado_por = uu.id

ORDER BY ra.id DESC
";

$resultado = $conexao->query($sql);

$dados = [];

while($row = $resultado->fetch_assoc()) {

    $dados[] = $row;
}

echo json_encode($dados);

?>