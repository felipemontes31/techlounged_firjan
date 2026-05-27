<?php

require_once(__DIR__ ."/../../config/conexao.php");
require_once(__DIR__ ."/../../utils/json.php");
require_once(__DIR__ ."/../../middleware/permissao.php");

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
$eh_publico = filter_var($_POST['eh_publico'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
$url_imagem = trim($_POST['url_imagem']);

if (!empty($url_imagem)) {

    $url_imagem = filter_var($url_imagem, FILTER_VALIDATE_URL);

    if ($url_imagem === false) {
        die("URL da imagem inválida.");
    }

} else {
    $url_imagem = null;
}

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
    eh_publico,
    url_imagem,
    criado_por,
    atualizado_por
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "iiissssiii",
    $id_eixo,
    $id_periodicidade,
    $id_publico_alvo,
    $nome_projeto,
    $objetivo,
    $observacoes,
    $eh_publico,
    $url_imagem,
    $usuario,
    $usuario
);

if (!$stmt->execute()) {

    respostaJSON(false, "Erro ao criar atividade");

}

respostaJSON(true, "Atividade criada");

?>