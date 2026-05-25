<?php

require_once("../../middleware/permissao.php");
require_once("../../config/conexao.php");
require_once("../../utils/response.php");

verificarPermissao([
    "Administrador",
    "Bibliotecário"
]);

$id = intval($_POST['id'] ?? 0);

$titulo_curta = trim($_POST['titulo_curta'] ?? '');

$link = trim($_POST['link'] ?? '');

$detalhes_controle = trim($_POST['detalhes_controle'] ?? '');

$usuario = $_SESSION['usuario']['id'];

if (
    empty($id) ||
    empty($titulo_curta)
) {

    redirecionarPagina(
        "Preencha os campos obrigatórios.",
        "/techlounged/views/cine_biblioteca.php"
    );
}

$sql = "
UPDATE cine_biblioteca
SET

    titulo_curta = ?,
    link = ?,
    detalhes_controle = ?,
    atualizado_por = ?

WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "sssii",
    $titulo_curta,
    $link,
    $detalhes_controle,
    $usuario,
    $id
);

if (!$stmt->execute()) {

    redirecionarPagina(
        "Erro ao atualizar.",
        "/techlounged/views/cine_biblioteca.php"
    );
}

redirecionarPagina(
    "Atualizado com sucesso.",
    "/techlounged/views/cine_biblioteca.php"
);

?>