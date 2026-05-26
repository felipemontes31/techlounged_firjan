<?php

require_once(__DIR__ . "/../../middleware/permissao.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

verificarPermissao(["Administrador"]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respostaJSON(false, "Método inválido.");
}

$id = $_POST['id'] ?? null;

$nome_funcao = trim($_POST['nome_funcao'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

if (
    empty($id) ||
    empty($nome_funcao)
) {
    respostaJSON(false, "Dados inválidos.");
}

$sqlVerifica = "
SELECT id
FROM funcao
WHERE nome_funcao = ?
AND id != ?
";

$stmtVerifica = $conexao->prepare($sqlVerifica);

$stmtVerifica->bind_param(
    "si",
    $nome_funcao,
    $id
);

$stmtVerifica->execute();

$resultadoVerifica = $stmtVerifica->get_result();

if ($resultadoVerifica->num_rows > 0) {
    respostaJSON(
        false,
        "Já existe outra função com esse nome."
    );
}

$sql = "
UPDATE funcao
SET
    nome_funcao = ?,
    descricao = ?
WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ssi",
    $nome_funcao,
    $descricao,
    $id
);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        respostaJSON(
            true,
            "Função atualizada com sucesso."
        );
    }
}

respostaJSON(
    false,
    "Nenhuma alteração realizada."
);

?>