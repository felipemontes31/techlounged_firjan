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
        u.id_funcao,
        u.nome,
        u.sobrenome,
        u.email,
        u.matricula,
        u.sexo,
        u.id_curso,
        u.senha_hash,
        u.ativo,
        f.nome_funcao AS funcao
    FROM usuario u
    INNER JOIN funcao f ON f.id = u.id_funcao
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
    "id" => $usuario["id"],
    "id_funcao" => $usuario["id_funcao"],
    "funcao" => $usuario["funcao"],
    "nome" => $usuario["nome"],
    "sobrenome" => $usuario["sobrenome"] ?? "",
    "email" => $usuario["email"],
    "matricula" => $usuario["matricula"] ?? "",
    "sexo" => $usuario["sexo"] ?? "Prefiro não informar",
    "id_curso" => $usuario["id_curso"] ?? null
];

redirecionarPagina(
    "Login realizado com sucesso.",
    "/index.php"
);





?>