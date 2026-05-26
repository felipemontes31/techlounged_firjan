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
    nome_funcao,
    descricao
FROM funcao
WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    respostaJSON(false, "Função não encontrada.");
}

$dados = $resultado->fetch_assoc();

respostaJSON(
    true,
    "Função encontrada.",
    $dados
);

?>