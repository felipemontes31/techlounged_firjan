<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . "/../middleware/registro_atividade.php");
require_once(__DIR__ . "/../utils/json.php");

$model = new RegistroAtividade($conexao);

$acao = $_GET['acao'] ?? '';

$usuarioLogado = isset($_SESSION['usuario']);

$funcao = $usuarioLogado
    ? $_SESSION['usuario']['funcao']
    : null;


// =========================================================
// LISTAR
// =========================================================

if ($acao === 'listar') {

    $resultado = $model->listarTodos($usuarioLogado);

    $dados = [];

    while ($linha = $resultado->fetch_assoc()) {
        $dados[] = $linha;
    }

    respostaJSON(true, "Lista carregada.", $dados);
}


// =========================================================
// BUSCAR
// =========================================================

if ($acao === 'buscar') {

    $id = intval($_GET['id']);

    $resultado = $model->buscarPorId($id, $usuarioLogado);

    if ($resultado->num_rows === 0) {

        respostaJSON(false, "Registro não encontrado.");
    }

    respostaJSON(
        true,
        "Registro encontrado.",
        $resultado->fetch_assoc()
    );
}


// =========================================================
// VERIFICAÇÃO DE PERMISSÃO ADMIN/BIBLIOTECÁRIO
// =========================================================

$permitidos = ['Administrador', 'Bibliotecário'];

if (
    !$usuarioLogado ||
    !in_array($funcao, $permitidos)
) {

    respostaJSON(false, "Sem permissão.");
}


// =========================================================
// CRIAR
// =========================================================

if ($acao === 'criar') {

    $dados = [

        "id_atividade" => $_POST['id_atividade'],
        "id_espaco" => $_POST['id_espaco'],
        "data_execucao" => $_POST['data_execucao'],
        "data_finalizacao" => $_POST['data_finalizacao'],
        "tema_especifico" => $_POST['tema_especifico'],
        "status" => $_POST['status'],
        "publico_realizado" => $_POST['publico_realizado'],
        "publico_previsto" => $_POST['publico_previsto'],
        "criado_por" => $_SESSION['usuario']['id'],
        "atualizado_por" => $_SESSION['usuario']['id']

    ];

    $sucesso = $model->criar($dados);

    respostaJSON(
        $sucesso,
        $sucesso
            ? "Registro criado."
            : "Erro ao criar registro."
    );
}


// =========================================================
// EDITAR
// =========================================================

if ($acao === 'editar') {

    $id = intval($_POST['id']);

    $dados = [

        "id_atividade" => $_POST['id_atividade'],
        "id_espaco" => $_POST['id_espaco'],
        "data_execucao" => $_POST['data_execucao'],
        "data_finalizacao" => $_POST['data_finalizacao'],
        "tema_especifico" => $_POST['tema_especifico'],
        "status" => $_POST['status'],
        "publico_realizado" => $_POST['publico_realizado'],
        "publico_previsto" => $_POST['publico_previsto'],
        "atualizado_por" => $_SESSION['usuario']['id']

    ];

    $sucesso = $model->atualizar($id, $dados);

    respostaJSON(
        $sucesso,
        $sucesso
            ? "Registro atualizado."
            : "Erro ao atualizar."
    );
}


// =========================================================
// EXCLUIR
// =========================================================

if ($acao === 'excluir') {

    $id = intval($_POST['id']);

    $sucesso = $model->excluir($id);

    respostaJSON(
        $sucesso,
        $sucesso
            ? "Registro removido."
            : "Erro ao remover."
    );
}

?>