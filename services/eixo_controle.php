<?php

require_once(__DIR__ . "/../config/conexao.php");
//require_once(__DIR__ . "/../middleware/permissao.php");
require_once(__DIR__ . "/../utils/json.php");
require_once(__DIR__ . "/../utils/response.php");
require_once(__DIR__ . "/../models/Eixo.php");

// Proteção: Só permite acesso se passar pelo middleware de autenticação
//verificarPermissao(); 

$model = new Eixo($conexao);
$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'criar':
        $nome_eixo = trim($_POST['nome_eixo'] ?? '');
        $observacao = trim($_POST['observacao'] ?? '');
        
        if (empty($nome_eixo)) {
            respostaJSON(false, "O nome do eixo é obrigatório.");
        }
        
        // Se a observação estiver vazia, guarda como NULL no banco
        $observacao = empty($observacao) ? null : $observacao;

        if ($model->criar($nome_eixo, $observacao)) {
            respostaJSON(true, "Eixo criado com sucesso!");
        } else {
            respostaJSON(false, "Erro ao criar eixo (pode ser que este nome já exista).");
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
        $nome_eixo = trim($_POST['nome_eixo'] ?? '');
        $observacao = trim($_POST['observacao'] ?? '');

        if ($id <= 0 || empty($nome_eixo)) {
            respostaJSON(false, "Dados inválidos para atualização.");
        }

        $observacao = empty($observacao) ? null : $observacao;

        if ($model->atualizar($id, $nome_eixo, $observacao)) {
            respostaJSON(true, "Eixo atualizado com sucesso!");
        } else {
            respostaJSON(false, "Erro ao atualizar o eixo.");
        }
        break;

    case 'deletar':
        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            respostaJSON(false, "ID inválido.");
        }

        if ($model->deletar($id)) {
            respostaJSON(true, "Eixo excluído com sucesso!");
        } else {
            respostaJSON(false, "Erro ao excluir. O eixo pode estar associado a um ou mais projetos/atividades.");
        }
        break;

    default:
        respostaJSON(false, "Ação não permitida.");
        break;
}