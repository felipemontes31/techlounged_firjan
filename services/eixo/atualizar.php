<?php

require_once(__DIR__ . "/../../middleware/permissao.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

verificarPermissao([
    "Administrador",
    "Bibliotecário"
]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respostaJSON(false, "Método inválido.");
}

$id = $_POST['id'] ?? null;

$nome_eixo = trim($_POST['nome_eixo'] ?? '');
$observacao = trim($_POST['observacao'] ?? '');

if (
    empty($id) ||
    empty($nome_eixo)
) {
    respostaJSON(false, "Dados inválidos.");
}

$sqlVerifica = "
SELECT id
FROM eixo
WHERE nome_eixo = ?
AND id != ?
";

$stmtVerifica = $conexao->prepare($sqlVerifica);

$stmtVerifica->bind_param(
    "si",
    $nome_eixo,
    $id
);

$stmtVerifica->execute();

$resultadoVerifica = $stmtVerifica->get_result();

if ($resultadoVerifica->num_rows > 0) {

    respostaJSON(
        false,
        "Já existe outro eixo com esse nome."
    );
}

$sql = "
UPDATE eixo
SET
    nome_eixo = ?,
    observacao = ?
WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ssi",
    $nome_eixo,
    $observacao,
    $id
);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        respostaJSON(
            true,
            "Eixo atualizado com sucesso."
        );
    }
}

respostaJSON(
    false,
    "Nenhuma alteração realizada."
);

?>