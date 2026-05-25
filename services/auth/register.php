<?php

require_once("../../middleware/permissao.php");
require_once("../../config/conexao.php");
require_once("../../utils/response.php");

verificarPermissao([
    "Administrador"
]);

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');
$id_funcao = intval($_POST['id_funcao'] ?? 0);

if (
    empty($nome) ||
    empty($email) ||
    empty($senha) ||
    empty($id_funcao)
) {

    redirecionarPagina(
        "Preencha todos os campos.",
        "/techlounged/views/usuarios.php"
    );
}

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
        "/techlounged/views/usuarios.php"
    );
}

$senhaHash = password_hash(
    $senha,
    PASSWORD_DEFAULT
);

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
        "Erro ao cadastrar usuário.",
        "/techlounged/views/usuarios.php"
    );
}

redirecionarPagina(
    "Usuário cadastrado com sucesso.",
    "/techlounged/views/usuarios.php"
);

?>