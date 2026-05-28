<?php

require_once(__DIR__ . "/../../middleware/auth.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../config/app.php");
require_once(__DIR__ . "/../../utils/response.php");

$idUsuario = intval($_SESSION['usuario']['id'] ?? 0);

if ($idUsuario <= 0) {
    redirecionarPagina("Sessão inválida. Faça login novamente.", BASE_URL . "/../views/login.php");
}

$nome = trim($_POST['nome'] ?? '');
$sobrenome = trim($_POST['sobrenome'] ?? '');
$email = trim($_POST['email'] ?? '');
$matricula = trim($_POST['matricula'] ?? '');
$sexo = trim($_POST['sexo'] ?? 'Prefiro não informar');

$sexosPermitidos = ['Masculino', 'Feminino', 'Prefiro não informar'];

if ($nome === '' || $email === '') {
    redirecionarPagina("Nome e e-mail são obrigatórios.", BASE_URL . "/../views/perfil.php");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirecionarPagina("Informe um e-mail válido.", BASE_URL . "/../views/perfil.php");
}

if (!in_array($sexo, $sexosPermitidos)) {
    $sexo = 'Prefiro não informar';
}

if (mb_strlen($nome) > 30) {
    redirecionarPagina("O nome deve ter no máximo 30 caracteres.", BASE_URL . "/../views/perfil.php");
}

if (mb_strlen($sobrenome) > 100) {
    redirecionarPagina("O sobrenome deve ter no máximo 100 caracteres.", BASE_URL . "/../views/perfil.php");
}

if ($matricula !== '' && mb_strlen($matricula) > 10) {
    redirecionarPagina("A matrícula deve ter no máximo 10 caracteres.", BASE_URL . "/../views/perfil.php");
}

// Verifica se o e-mail já pertence a outro usuário.
$stmtEmail = $conexao->prepare("SELECT id FROM usuario WHERE email = ? AND id <> ? LIMIT 1");
$stmtEmail->bind_param("si", $email, $idUsuario);
$stmtEmail->execute();
$emailExistente = $stmtEmail->get_result()->fetch_assoc();

if ($emailExistente) {
    redirecionarPagina("Este e-mail já está sendo utilizado por outro usuário.", BASE_URL . "/../views/perfil.php");
}

// Verifica se a matrícula já pertence a outro usuário.
if ($matricula !== '') {
    $stmtMatricula = $conexao->prepare("SELECT id FROM usuario WHERE matricula = ? AND id <> ? LIMIT 1");
    $stmtMatricula->bind_param("si", $matricula, $idUsuario);
    $stmtMatricula->execute();
    $matriculaExistente = $stmtMatricula->get_result()->fetch_assoc();

    if ($matriculaExistente) {
        redirecionarPagina("Esta matrícula já está sendo utilizada por outro usuário.", BASE_URL . "/../views/perfil.php");
    }
}

$matriculaBanco = $matricula !== '' ? $matricula : null;
$sobrenomeBanco = $sobrenome !== '' ? $sobrenome : null;

$stmtAtualizar = $conexao->prepare("
    UPDATE usuario
       SET nome = ?,
           sobrenome = ?,
           sexo = ?,
           email = ?,
           matricula = ?
     WHERE id = ?
");

$stmtAtualizar->bind_param(
    "sssssi",
    $nome,
    $sobrenomeBanco,
    $sexo,
    $email,
    $matriculaBanco,
    $idUsuario
);

if (!$stmtAtualizar->execute()) {
    redirecionarPagina("Erro ao atualizar o perfil: " . $stmtAtualizar->error, BASE_URL . "/../views/perfil.php");
}

// Atualiza a sessão para refletir os novos dados sem exigir novo login.
$_SESSION['usuario']['nome'] = $nome;
$_SESSION['usuario']['sobrenome'] = $sobrenomeBanco;
$_SESSION['usuario']['sexo'] = $sexo;
$_SESSION['usuario']['email'] = $email;
$_SESSION['usuario']['matricula'] = $matriculaBanco;

redirecionarPagina("Perfil atualizado com sucesso.", BASE_URL . "/../views/perfil.php");

?>
