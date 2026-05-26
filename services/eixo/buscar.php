<?php

require_once(__DIR__ . "/../../middleware/auth.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

$id = $_GET['id'] ?? null;

if (empty($id)) {
    respostaJSON(false, "ID não informado.");
}

$sql = "
SELECT
    id,
    nome_eixo,
    observacao
FROM eixo
WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    respostaJSON(
        false,
        "Eixo não encontrado."
    );
}

$dados = $resultado->fetch_assoc();

respostaJSON(
    true,
    "Eixo encontrado.",
    $dados
);

?>