<?php

require_once("../../config/conexao.php");
require_once("../../utils/response.php");

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (
    empty($nome) ||
    empty($email) ||
    empty($senha)
) {

    redirecionarPagina(
        "Preencha todos os campos.",
        "/techlounged/views/cadastro.php"
    );
}

// =====================================================
// VALIDAÇÃO DE EMAIL
// =====================================================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    redirecionarPagina(
        "Email inválido.",
        "/techlounged/views/cadastro.php"
    );
}

// =====================================================
// VERIFICA EMAIL EXISTENTE
// =====================================================

$sqlVerifica = "
SELECT id
FROM usuario
WHERE email = ?
LIMIT 1
";

$stmtVerifica = $conexao->prepare($sqlVerifica);

$stmtVerifica->bind_param("s", $email);

$stmtVerifica->execute();

$resultado = $stmtVerifica->get_result();

if ($resultado->num_rows > 0) {

    redirecionarPagina(
        "Email já cadastrado.",
        "/techlounged/views/cadastro.php"
    );
}

// =====================================================
// FUNÇÃO PADRÃO = ALUNO/PÚBLICO
// =====================================================

$id_funcao = 4;

// =====================================================
// HASH DA SENHA
// =====================================================

$senhaHash = password_hash(
    $senha,
    PASSWORD_DEFAULT
);

// =====================================================
// INSERT
// =====================================================

$sql = "
INSERT INTO usuario
(
    id_funcao,
    nome,
    email,
    senha_hash
)
VALUES (?, ?, ?, ?)
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "isss",
    $id_funcao,
    $nome,
    $email,
    $senhaHash
);

if (!$stmt->execute()) {

    redirecionarPagina(
        "Erro ao criar conta.",
        "/techlounged/views/cadastro.php"
    );
}

// =====================================================
// SUCESSO
// =====================================================

redirecionarPagina(
    "Conta criada com sucesso.",
    "/techlounged/views/login.php"
);

?>