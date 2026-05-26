<?php

require_once(__DIR__ . "/../../middleware/permissao.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

verificarPermissao(["Administrador"]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respostaJSON(false, "Método inválido.");
}

$id = $_POST['id'] ?? null;

if (empty($id)) {
    respostaJSON(false, "ID não informado.");
}

$sqlUsuarios = "
SELECT id
FROM usuario
WHERE id_funcao = ?
LIMIT 1
";

$stmtUsuarios = $conexao->prepare($sqlUsuarios);

$stmtUsuarios->bind_param(
    "i",
    $id
);

$stmtUsuarios->execute();

$resultadoUsuarios = $stmtUsuarios->get_result();

if ($resultadoUsuarios->num_rows > 0) {

    respostaJSON(
        false,
        "Não é possível excluir uma função vinculada a usuários."
    );
}

$sql = "
DELETE FROM funcao
WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        respostaJSON(
            true,
            "Função removida com sucesso."
        );
    }
}

respostaJSON(
    false,
    "Função não encontrada."
);

?>