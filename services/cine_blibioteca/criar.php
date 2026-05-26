<?php

require_once(__DIR__ . "/../../middleware/auth.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respostaJSON(false, "Método inválido.");
}

$id_registro_atividade = $_POST['id_registro_atividade'] ?? null;
$titulo_curta = trim($_POST['titulo_curta'] ?? '');
$link = trim($_POST['link'] ?? '');
$detalhes_controle = trim($_POST['detalhes_controle'] ?? '');

if (
    empty($id_registro_atividade) ||
    empty($titulo_curta)
) {
    respostaJSON(false, "Preencha os campos obrigatórios.");
}

$id_usuario = $_SESSION['usuario']['id'];

$sqlValidacao = "
SELECT ra.id
FROM registro_atividade ra
INNER JOIN atividade a
    ON a.id = ra.id_atividade
WHERE ra.id = ?
AND a.nome_projeto = 'Cine Biblioteca'
";

$stmtValidacao = $conexao->prepare($sqlValidacao);
$stmtValidacao->bind_param("i", $id_registro_atividade);
$stmtValidacao->execute();

$resultadoValidacao = $stmtValidacao->get_result();

if ($resultadoValidacao->num_rows === 0) {
    respostaJSON(
        false,
        "O registro informado não pertence ao projeto Cine Biblioteca."
    );
}

$sql = "
INSERT INTO cine_biblioteca (
    id_registro_atividade,
    titulo_curta,
    link,
    detalhes_controle,
    criado_por,
    atualizado_por
)
VALUES (?, ?, ?, ?, ?, ?)
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "isssii",
    $id_registro_atividade,
    $titulo_curta,
    $link,
    $detalhes_controle,
    $id_usuario,
    $id_usuario
);

if ($stmt->execute()) {

    respostaJSON(
        true,
        "Registro criado com sucesso."
    );
}

respostaJSON(
    false,
    "Erro ao criar registro."
);

?>