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

$id_atividade = $_POST['id_atividade'];
$id_espaco = $_POST['id_espaco'];

$data_execucao = $_POST['data_execucao'];
$data_finalizacao = $_POST['data_finalizacao'];

$tema_especifico = trim($_POST['tema_especifico']);

$status = $_POST['status'];

$publico_previsto = $_POST['publico_previsto'];
$publico_realizado = $_POST['publico_realizado'];

$usuario = $_SESSION['usuario']['id'];

$sql = "
UPDATE registro_atividade
SET

    id_atividade = ?,
    id_espaco = ?,
    data_execucao = ?,
    data_finalizacao = ?,
    tema_especifico = ?,
    status = ?,
    publico_previsto = ?,
    publico_realizado = ?,
    atualizado_por = ?

WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "iissssiiii",
    $id_atividade,
    $id_espaco,
    $data_execucao,
    $data_finalizacao,
    $tema_especifico,
    $status,
    $publico_previsto,
    $publico_realizado,
    $usuario,
    $id
);

if ($stmt->execute()) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Registro atualizado"
    ]);

} else {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao atualizar"
    ]);
}

?>