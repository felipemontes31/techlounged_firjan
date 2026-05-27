<?php

// Importa configurações globais, utilitários e a model correspondente
require_once(__DIR__ . "/../config/conexao.php");
require_once(__DIR__ . "/../middleware/permissao.php");
require_once(__DIR__ . "/../utils/json.php");
require_once(__DIR__ . "/../utils/response.php");
require_once(__DIR__ . "/../models/PublicoAlvo.php");

// Proteção: Garante que o usuário esteja autenticado na sessão
//verificarPermissao(); 

$model = new PublicoAlvo($conexao);
$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'criar':
        $nome_publico = trim($_POST['nome_publico'] ?? '');
        
        if (empty($nome_publico)) {
            respostaJSON(false, "O nome do público alvo é obrigatório.");
        }

        if ($model->criar($nome_publico)) {
            respostaJSON(true, "Público alvo criado com sucesso!");
        } else {
            respostaJSON(false, "Erro ao criar público alvo (verifique se este nome já existe).");
        }
        break;

    case 'listar':
        $dados = $model->listarTodos();
        respostaJSON(true, "Dados carregados com sucesso.", $dados);
        break;

    case 'buscar':
        $id = intval($_GET['id'] ?? 0);
        $dado = $model->buscarPorId($id);
        
        if ($dado) {
            respostaJSON(true, "Registro encontrado.", $dado);
        } else {
            respostaJSON(false, "Registro não encontrado.");
        }
        break;

    case 'atualizar':
        $id = intval($_POST['id'] ?? 0);
        $nome_publico = trim($_POST['nome_publico'] ?? '');

        if ($id <= 0 || empty($nome_publico)) {
            respostaJSON(false, "Dados inválidos para a atualização.");
        }

        if ($model->atualizar($id, $nome_publico)) {
            respostaJSON(true, "Público alvo atualizado com sucesso!");
        } else {
            respostaJSON(false, "Erro ao atualizar o público alvo.");
        }
        break;

    case 'deletar':
        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            respostaJSON(false, "ID inválido.");
        }

        if ($model->deletar($id)) {
            respostaJSON(true, "Público alvo excluído com sucesso!");
        } else {
            respostaJSON(false, "Não foi possível excluir. O registro pode estar associado a uma Atividade ativa.");
        }
        break;

    default:
        respostaJSON(false, "Ação não reconhecida.");
        break;
}