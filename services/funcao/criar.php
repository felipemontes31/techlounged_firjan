<?php

require_once(__DIR__ . "/../../middleware/permissao.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

verificarPermissao(["Administrador"]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respostaJSON(false, "Método inválido.");
}

$nome_funcao = trim($_POST['nome_funcao'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

if (empty($nome_funcao)) {
    respostaJSON(false, "Nome da função é obrigatório.");
}

$sqlVerifica = "
SELECT id
FROM funcao
WHERE nome_funcao = ?
";

$stmtVerifica = $conexao->prepare($sqlVerifica);

$stmtVerifica->bind_param(
    "s",
    $nome_funcao
);

$stmtVerifica->execute();

$resultado = $stmtVerifica->get_result();

if ($resultado->num_rows > 0) {
    respostaJSON(false, "Já existe uma função com esse nome.");
}

$sql = "
INSERT INTO funcao (
    nome_funcao,
    descricao
)
VALUES (?, ?)
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ss",
    $nome_funcao,
    $descricao
);

if ($stmt->execute()) {

    respostaJSON(
        true,
        "Função criada com sucesso."
    );
}

respostaJSON(
    false,
    "Erro ao criar função."
);

?>