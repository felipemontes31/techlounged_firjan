<?php

require_once(__DIR__ ."/../../config/conexao.php");

$sql = "
SELECT

    a.id,
    a.nome_projeto,
    a.objetivo,
    a.observacoes_gerais,
    a.eh_publico,
    a.url_imagem,
    a.data_criacao,

    e.nome_eixo,
    p.descricao AS periodicidade,
    pa.nome_publico,

    uc.nome AS criado_por_nome,
    uu.nome AS atualizado_por_nome

FROM atividade a

INNER JOIN eixo e
ON a.id_eixo = e.id

INNER JOIN periodicidade p
ON a.id_periodicidade = p.id

INNER JOIN publico_alvo pa
ON a.id_publico_alvo = pa.id

INNER JOIN usuario uc
ON a.criado_por = uc.id

INNER JOIN usuario uu
ON a.atualizado_por = uu.id

ORDER BY a.id DESC
";

$resultado = $conexao->query($sql);

$dados = [];

while($row = $resultado->fetch_assoc()) {

    $dados[] = $row;
}

respostaJSON(
    true,
    "Atividades carregadas.",
    $dados
);

?>