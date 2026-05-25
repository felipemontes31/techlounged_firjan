<?php

require_once("../../middleware/auth.php");
require_once("../../config/conexao.php");

header("Content-Type: application/json");

$id = intval($_GET['id'] ?? 0);

$sql = "
SELECT *
FROM cine_biblioteca
WHERE id = ?
LIMIT 1
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Registro não encontrado."
    ]);

    exit;
}

$dados = $resultado->fetch_assoc();

echo json_encode([
    "sucesso" => true,
    "dados" => $dados
]);

?>