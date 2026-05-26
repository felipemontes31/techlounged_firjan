<?php

require_once(__DIR__ . "/../../middleware/auth.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respostaJSON(false, "Método inválido.");
}

$id = $_POST['id'] ?? null;

$titulo_curta = trim($_POST['titulo_curta'] ?? '');
$link = trim($_POST['link'] ?? '');
$detalhes_controle = trim($_POST['detalhes_controle'] ?? '');

if (
    empty($id) ||
    empty($titulo_curta)
) {
    respostaJSON(false, "Dados inválidos.");
}

$id_usuario = $_SESSION['usuario']['id'];

$sql = "
UPDATE cine_biblioteca cb

INNER JOIN registro_atividade ra
    ON ra.id = cb.id_registro_atividade

INNER JOIN atividade a
    ON a.id = ra.id_atividade

SET
    cb.titulo_curta = ?,
    cb.link = ?,
    cb.detalhes_controle = ?,
    cb.atualizado_por = ?

WHERE cb.id = ?
AND a.nome_projeto = 'Cine Biblioteca'
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "sssii",
    $titulo_curta,
    $link,
    $detalhes_controle,
    $id_usuario,
    $id
);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        respostaJSON(
            true,
            "Registro atualizado com sucesso."
        );
    }
}

respostaJSON(
    false,
    "Registro não encontrado ou não alterado."
);

?>