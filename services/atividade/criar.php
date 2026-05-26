<?php

require_once("../../config/conexao.php");
require_once("../../utils/json.php");
require_once("../../middleware/permissao.php");

verificarPermissao([
    "Administrador",
    "Bibliotecário"
]);

$id_eixo = $_POST['id_eixo'];
$id_periodicidade = $_POST['id_periodicidade'];
$id_publico_alvo = $_POST['id_publico_alvo'];
$nome_projeto = trim($_POST['nome_projeto']);
$objetivo = trim($_POST['objetivo']);
$observacoes = trim($_POST['observacoes']);

$usuario = $_SESSION['usuario']['id'];

$sql = "
INSERT INTO atividade
(
    id_eixo,
    id_periodicidade,
    id_publico_alvo,
    nome_projeto,
    objetivo,
    observacoes_gerais,
    criado_por,
    atualizado_por
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "iiisssii",
    $id_eixo,
    $id_periodicidade,
    $id_publico_alvo,
    $nome_projeto,
    $objetivo,
    $observacoes,
    $usuario,
    $usuario
);

if (!$stmt->execute()) {

    respostaJSON(false, "Erro ao criar atividade");

}

respostaJSON(true, "Atividade criada");

?>