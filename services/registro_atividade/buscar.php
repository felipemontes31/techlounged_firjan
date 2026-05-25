<?php

require_once("../../config/conexao.php");

header("Content-Type: application/json");

$id = $_GET['id'];

$sql = "
SELECT *
FROM registro_atividade
WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Registro não encontrado"
    ]);

    exit;
}

$registro = $resultado->fetch_assoc();

echo json_encode([
    "sucesso" => true,
    "dados" => $registro
]);

?>