<?php

session_start();

require_once("../../config/conexao.php");

header("Content-Type: application/json");

if (!isset($_SESSION['usuario'])) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não autenticado"
    ]);

    exit;
}

$id = $_POST['id'];

$sql = "
DELETE FROM registro_atividade
WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Registro deletado"
    ]);

} else {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao deletar"
    ]);
}

?>