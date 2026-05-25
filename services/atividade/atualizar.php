<?php

session_start();

require_once("../../config/conexao.php");

$id = $_POST['id'];

$nome = $_POST['nome_projeto'];

$objetivo = $_POST['objetivo'];

$usuario = $_SESSION['usuario']['id'];

$sql = "
UPDATE atividade
SET
    nome_projeto = ?,
    objetivo = ?,
    atualizado_por = ?
WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ssii",
    $nome,
    $objetivo,
    $usuario,
    $id
);

$stmt->execute();

if (!$stmt->execute()) {

    respostaJSON(false, "Erro ao atualizar atividade");

}

respostaJSON(true, "Atividade atualizada");


?>
