<?php

require_once(__DIR__ . "/../../middleware/auth.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

$sql = "
SELECT
    cb.id,
    cb.titulo_curta,
    cb.link,
    cb.detalhes_controle,
    ra.tema_especifico,
    ra.data_execucao
FROM cine_biblioteca cb

INNER JOIN registro_atividade ra
    ON ra.id = cb.id_registro_atividade

INNER JOIN atividade a
    ON a.id = ra.id_atividade

WHERE a.nome_projeto = 'Cine Biblioteca'

ORDER BY cb.id DESC
";

$resultado = $conexao->query($sql);

$dados = [];

while ($linha = $resultado->fetch_assoc()) {
    $dados[] = $linha;
}

respostaJSON(
    true,
    "Lista carregada.",
    $dados
);


?>