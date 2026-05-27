<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ ."/../../config/conexao.php");
require_once(__DIR__ ."/../../utils/response.php");

$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (empty($email) || empty($senha)) {

    redirecionarPagina(
        "Preencha todos os campos.",
        "/views/login.php"
    );
}

$sql = "
SELECT
    u.id,
    u.nome,
    u.email,
    u.senha_hash,
    u.ativo,
    f.nome_funcao

FROM usuario u

INNER JOIN funcao f
ON u.id_funcao = f.id

WHERE u.email = ?
LIMIT 1
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    redirecionarPagina(
        "Usuário não encontrado.",
        "/views/login.php"
    );
}

$usuario = $resultado->fetch_assoc();

if (!$usuario['ativo']) {

    redirecionarPagina(
        "Usuário desativado.",
        "/views/login.php"
    );
}

if (!password_verify($senha, $usuario['senha_hash'])) {

    redirecionarPagina(
        "Senha inválida.",
        "/views/login.php"
    );
}


$_SESSION['usuario'] = [

    "id" => $usuario['id'],
    "nome" => $usuario['nome'],
    "email" => $usuario['email'],
    "funcao" => $usuario['nome_funcao']

];

redirecionarPagina(
    "Login realizado com sucesso.",
    "/views/dashboard.php"
);





?>