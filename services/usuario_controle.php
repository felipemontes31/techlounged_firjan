<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . "/../config/conexao.php");
require_once(__DIR__ . "/../models/usuario.php");
require_once(__DIR__ . "/../utils/json.php");

$model = new Usuario($conexao);

$acao = $_GET['acao'] ?? '';

if (!isset($_SESSION['usuario'])) {

    respostaJSON(false, "Faça login.");
}

$usuario = $_SESSION['usuario'];

$idUsuario = $usuario['id'];

$funcao = $usuario['funcao'];

$isAdmin = ($funcao === 'Administrador');


// =========================================================
// LISTAR TODOS (ADMIN)
// =========================================================

if ($acao === 'listar') {

    if (!$isAdmin) {

        respostaJSON(false, "Sem permissão.");
    }

    $resultado = $model->listarTodos();

    $dados = [];

    while ($linha = $resultado->fetch_assoc()) {

        $dados[] = $linha;
    }

    respostaJSON(true, "Usuários encontrados.", $dados);
}


// =========================================================
// BUSCAR
// =========================================================

if ($acao === 'buscar') {

    $id = intval($_GET['id']);

    if (!$isAdmin && $id != $idUsuario) {

        respostaJSON(false, "Sem permissão.");
    }

    $resultado = $model->buscarPorId($id);

    if ($resultado->num_rows === 0) {

        respostaJSON(false, "Usuário não encontrado.");
    }

    respostaJSON(
        true,
        "Usuário encontrado.",
        $resultado->fetch_assoc()
    );
}


// =========================================================
// CRIAR (ADMIN)
// =========================================================

if ($acao === 'criar') {

    if (!$isAdmin) {

        respostaJSON(false, "Sem permissão.");
    }

    $email = trim($_POST['email']);

    if ($model->emailExiste($email)) {

        respostaJSON(false, "Email já cadastrado.");
    }

    $dados = [

        "id_funcao" => intval($_POST['id_funcao']),
        "nome" => trim($_POST['nome']),
        "email" => $email,
        "senha_hash" => password_hash($_POST['senha'], PASSWORD_DEFAULT),
        "ativo" => intval($_POST['ativo'])

    ];

    $sucesso = $model->criar($dados);

    respostaJSON(
        $sucesso,
        $sucesso
            ? "Usuário criado."
            : "Erro ao criar usuário."
    );
}


// =========================================================
// EDITAR
// =========================================================

if ($acao === 'editar') {

    $id = intval($_POST['id']);

    // =====================================================
    // USUÁRIO COMUM
    // =====================================================

    if (!$isAdmin && $id != $idUsuario) {

        respostaJSON(false, "Sem permissão.");
    }

    $email = trim($_POST['email']);

    if ($model->emailExiste($email, $id)) {

        respostaJSON(false, "Email já utilizado.");
    }

    // =====================================================
    // ADMIN
    // =====================================================

    if ($isAdmin) {

        $dados = [

            "id_funcao" => intval($_POST['id_funcao']),
            "nome" => trim($_POST['nome']),
            "email" => $email,
            "senha_hash" => password_hash($_POST['senha'], PASSWORD_DEFAULT),
            "ativo" => intval($_POST['ativo'])

        ];

        $sucesso = $model->atualizarAdmin($id, $dados);

    } else {

        // =================================================
        // PRÓPRIA CONTA
        // =================================================

        $dados = [

            "nome" => trim($_POST['nome']),
            "email" => $email,
            "senha_hash" => password_hash($_POST['senha'], PASSWORD_DEFAULT)

        ];

        $sucesso = $model->atualizarProprio($id, $dados);
    }

    respostaJSON(
        $sucesso,
        $sucesso
            ? "Usuário atualizado."
            : "Erro ao atualizar."
    );
}


// =========================================================
// EXCLUIR
// =========================================================

if ($acao === 'excluir') {

    $id = intval($_POST['id']);

    if (!$isAdmin && $id != $idUsuario) {

        respostaJSON(false, "Sem permissão.");
    }

    $sucesso = $model->excluir($id);

    // Logout automático se apagar a própria conta
    if ($sucesso && $id == $idUsuario) {

        session_destroy();
    }

    respostaJSON(
        $sucesso,
        $sucesso
            ? "Usuário removido."
            : "Erro ao remover."
    );
}

?>