<?php

require_once(__DIR__ . "/../config/conexao.php");
//require_once(__DIR__ . "/../middleware/permissao.php");
require_once(__DIR__ . "/../utils/json.php");
require_once(__DIR__ . "/../utils/response.php");
require_once(__DIR__ . "/../middleware/Espaco.php");

// Proteção de rota herdada do seu sistema de autenticação
//verificarPermissao(); 

$model = new Espaco($conexao);
$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'criar':
        $nome_espaco = trim($_POST['nome_espaco'] ?? '');
        $capacidade_maxima = isset($_POST['capacidade_maxima']) ? intval($_POST['capacidade_maxima']) : null;
        
        if (empty($nome_espaco)) {
            respostaJSON(false, "O nome do espaço é obrigatório.");
        }
        
        if ($capacidade_maxima !== null && $capacidade_maxima <= 0) {
            respostaJSON(false, "A capacidade máxima deve ser um número maior que zero.");
        }

        if ($model->criar($nome_espaco, $capacidade_maxima)) {
            respostaJSON(true, "Espaço cadastrado com sucesso!");
        } else {
            respostaJSON(false, "Erro ao cadastrar espaço (verifique se o nome já existe).");
        }
        break;

    case 'listar':
        $dados = $model->listarTodos();
        respostaJSON(true, "Dados carregados.", $dados);
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
        $nome_espaco = trim($_POST['nome_espaco'] ?? '');
        $capacidade_maxima = isset($_POST['capacidade_maxima']) ? intval($_POST['capacidade_maxima']) : null;

        if ($id <= 0 || empty($nome_espaco)) {
            respostaJSON(false, "Dados inválidos para a atualização.");
        }

        if ($capacidade_maxima !== null && $capacidade_maxima <= 0) {
            respostaJSON(false, "A capacidade máxima deve ser um número maior que zero.");
        }

        if ($model->atualizar($id, $nome_espaco, $capacidade_maxima)) {
            respostaJSON(true, "Espaço atualizado com sucesso!");
        } else {
            respostaJSON(false, "Erro ao atualizar o espaço.");
        }
        break;

    case 'deletar':
        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            respostaJSON(false, "ID inválido.");
        }

        if ($model->deletar($id)) {
            respostaJSON(true, "Espaço excluído com sucesso!");
        } else {
            respostaJSON(false, "Não foi possível excluir. O espaço pode estar vinculado a algum Registro de Atividade.");
        }
        break;

    default:
        respostaJSON(false, "Ação não permitida.");
        break;
}