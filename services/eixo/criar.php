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

$nome_eixo = trim($_POST['nome_eixo'] ?? '');
$observacao = trim($_POST['observacao'] ?? '');

if (empty($nome_eixo)) {
    respostaJSON(false, "Nome do eixo é obrigatório.");
}

$sqlVerifica = "
SELECT id
FROM eixo
WHERE nome_eixo = ?
";

$stmtVerifica = $conexao->prepare($sqlVerifica);

$stmtVerifica->bind_param(
    "s",
    $nome_eixo
);

$stmtVerifica->execute();

$resultadoVerifica = $stmtVerifica->get_result();

if ($resultadoVerifica->num_rows > 0) {

    respostaJSON(
        false,
        "Já existe um eixo com esse nome."
    );
}

$sql = "
INSERT INTO eixo (
    nome_eixo,
    observacao
)
VALUES (?, ?)
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ss",
    $nome_eixo,
    $observacao
);

if ($stmt->execute()) {

    respostaJSON(
        true,
        "Eixo criado com sucesso."
    );
}

respostaJSON(
    false,
    "Erro ao criar eixo."
);

?>