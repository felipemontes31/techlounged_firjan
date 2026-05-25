<?php

require_once("../../middleware/permissao.php");
require_once("../../config/conexao.php");
require_once("../../utils/response.php");

verificarPermissao([
    "Administrador",
    "Bibliotecário"
]);

$id_registro_atividade = intval($_POST['id_registro_atividade'] ?? 0);

$titulo_curta = trim($_POST['titulo_curta'] ?? '');

$link = trim($_POST['link'] ?? '');

$detalhes_controle = trim($_POST['detalhes_controle'] ?? '');

$usuario = $_SESSION['usuario']['id'];

if (
    empty($id_registro_atividade) ||
    empty($titulo_curta)
) {

    redirecionarPagina(
        "Preencha os campos obrigatórios.",
        "/techlounged/views/cine_biblioteca.php"
    );
}

$sqlVerifica = "
SELECT id
FROM cine_biblioteca
WHERE id_registro_atividade = ?
LIMIT 1
";

$stmtVerifica = $conexao->prepare($sqlVerifica);

$stmtVerifica->bind_param(
    "i",
    $id_registro_atividade
);

$stmtVerifica->execute();

$resultado = $stmtVerifica->get_result();

if ($resultado->num_rows > 0) {

    redirecionarPagina(
        "Este registro já possui um Cine Biblioteca.",
        "/techlounged/views/cine_biblioteca.php"
    );
}

$sql = "
INSERT INTO cine_biblioteca
(
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
    $usuario,
    $usuario
);

if (!$stmt->execute()) {

    redirecionarPagina(
        "Erro ao criar Cine Biblioteca.",
        "/techlounged/views/cine_biblioteca.php"
    );
}

redirecionarPagina(
    "Cine Biblioteca criado com sucesso.",
    "/techlounged/views/cine_biblioteca.php"
);

?>