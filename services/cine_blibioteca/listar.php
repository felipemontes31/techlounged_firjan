<?php

require_once("../../middleware/auth.php");
require_once("../../config/conexao.php");

header("Content-Type: application/json");

$sql = "
SELECT

    cb.id,
    cb.titulo_curta,
    cb.link,
    cb.detalhes_controle,
    cb.data_criacao,

    ra.tema_especifico,
    ra.data_execucao,

    a.nome_projeto,

    uc.nome AS criado_por_nome,
    uu.nome AS atualizado_por_nome

FROM cine_biblioteca cb

INNER JOIN registro_atividade ra
ON cb.id_registro_atividade = ra.id

INNER JOIN atividade a
ON ra.id_atividade = a.id

INNER JOIN usuario uc
ON cb.criado_por = uc.id

INNER JOIN usuario uu
ON cb.atualizado_por = uu.id

ORDER BY cb.id DESC
";

$resultado = $conexao->query($sql);

$dados = [];

while($row = $resultado->fetch_assoc()) {

    $dados[] = $row;
}

echo json_encode($dados);

?>