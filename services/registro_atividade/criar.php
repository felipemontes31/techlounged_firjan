<?php

session_start();

require_once("../../config/conexao.php");
require_once("../../utils/json.php");
require_once("../../middleware/permissao.php");

verificarPermissao([
    "Administrador",
    "Bibliotecário"
]);


$id_atividade = $_POST['id_atividade'];
$id_espaco = $_POST['id_espaco'];
$data_execucao = $_POST['data_execucao'];
$data_finalizacao = $_POST['data_finalizacao'];
$tema_especifico = trim($_POST['tema_especifico']);
$status = $_POST['status'];
$publico_previsto = $_POST['publico_previsto'];

$usuario = $_SESSION['usuario']['id'];

$sql = "
INSERT INTO registro_atividade
(
    id_atividade,
    id_espaco,
    data_execucao,
    data_finalizacao,
    tema_especifico,
    status,
    publico_previsto,
    criado_por,
    atualizado_por
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "iissssiii",
    $id_atividade,
    $id_espaco,
    $data_execucao,
    $data_finalizacao,
    $tema_especifico,
    $status,
    $publico_previsto,
    $usuario,
    $usuario
);

if (!$stmt->execute()) {

    respostaJSON(false, "Erro ao criar Registro de Atividade");

}

respostaJSON(true, "Registro de Atividade criado!");

?>