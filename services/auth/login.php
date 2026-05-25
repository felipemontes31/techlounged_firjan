<?php

session_start();

require_once("../../config/conexao.php");
require_once("../../utils/response.php");

$email = trim($_POST['email']);
$senha = trim($_POST['senha']);

$sql = "
SELECT 
    u.*,
    f.nome_funcao

FROM usuario u

INNER JOIN funcao f
ON u.id_funcao = f.id

WHERE u.email = ?
AND u.ativo = 1
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    redirecionarPagina("Usuário não encontrado","../../views/login.php");

}

$usuario = $resultado->fetch_assoc();

if (!password_verify($senha, $usuario['senha_hash'])) {

    redirecionarPagina("Senha inválida","../../views/login.php");

}

$_SESSION['usuario'] = [

    "id" => $usuario['id'],
    "nome" => $usuario['nome'],
    "email" => $usuario['email'],
    "funcao" => $usuario['nome_funcao'],
    "id_funcao" => $usuario['id_funcao']

];

redirecionarPagina(
        "Bem vindo!",
        "../../views/dashboard.php"
    );

exit;

?>