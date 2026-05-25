<?php

require_once("../../config/conexao.php");

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuario
(id_funcao, nome, email, senha_hash)
VALUES (?, ?, ?, ?)";

$stmt = $conexao->prepare($sql);

$id_funcao = 1;

$stmt->bind_param(
    "isss",
    $id_funcao,
    $nome,
    $email,
    $senhaHash
);

if ($stmt->execute()) {

    echo "Usuário cadastrado";

} else {

    echo "Erro ao cadastrar";

}

?>