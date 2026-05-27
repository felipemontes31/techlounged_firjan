<?php

// Importa configurações globais e utilitários
require_once(__DIR__ . "/../config/conexao.php");
require_once(__DIR__ . "/../middleware/permissao.php");
require_once(__DIR__ . "/../utils/json.php");
require_once(__DIR__ . "/../utils/response.php");
require_once(__DIR__ . "/../middleware/Periodicidade.php");

// Proteção: Só permite usuários logados. (Opcional: adicione funções permitidas ex: verificarPermissao(['Admin', 'Gestor']))
//verificarPermissao(); 

$model = new Periodicidade($conexao);
$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'criar':
        $descricao = trim($_POST['descricao'] ?? '');
        
        if (empty($descricao)) {
            respostaJSON(false, "A descrição é obrigatória.");
        }

        if ($model->criar($descricao)) {
            respostaJSON(true, "Periodicidade criada com sucesso!");
        } else {
            respostaJSON(false, "Erro ao criar periodicidade (pode ser que já exista).");
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
        $descricao = trim($_POST['descricao'] ?? '');

        if ($id <= 0 || empty($descricao)) {
            respostaJSON(false, "Dados inválidos para atualização.");
        }

        if ($model->atualizar($id, $descricao)) {
            respostaJSON(true, "Periodicidade atualizada com sucesso!");
        } else {
            respostaJSON(false, "Erro ao atualizar a periodicidade.");
        }
        break;

    case 'deletar':
        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            respostaJSON(false, "ID inválido.");
        }

        if ($model->deletar($id)) {
            respostaJSON(true, "Periodicidade excluída com sucesso!");
        } else {
            respostaJSON(false, "Erro ao excluir. Verifique se ela está sendo usada em alguma Atividade.");
        }
        break;

    default:
        respostaJSON(false, "Ação não permitida.");
        break;
}