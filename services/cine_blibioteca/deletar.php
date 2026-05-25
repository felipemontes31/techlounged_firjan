<?php

require_once("../../middleware/permissao.php");
require_once("../../config/conexao.php");
require_once("../../utils/response.php");

verificarPermissao([
    "Administrador"
]);

$id = intval($_POST['id'] ?? 0);

if (empty($id)) {

    redirecionarPagina(
        "ID inválido.",
        "/techlounged/views/cine_biblioteca.php"
    );
}

$sql = "
DELETE FROM cine_biblioteca
WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {

    redirecionarPagina(
        "Erro ao deletar.",
        "/techlounged/views/cine_biblioteca.php"
    );
}

redirecionarPagina(
    "Registro deletado.",
    "/techlounged/views/cine_biblioteca.php"
);

?>