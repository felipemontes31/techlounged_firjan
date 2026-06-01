<?php

require_once(__DIR__ . "/../config/conexao.php");
require_once(__DIR__ . "/../middleware/permissao.php");
require_once(__DIR__ . "/../utils/json.php");
require_once(__DIR__ . "/../utils/response.php");
require_once(__DIR__ . "/../models/Atividade.php");

// Proteção da sessão
//verificarPermissao(); 

$model = new Atividade($conexao);
$acao = $_GET['acao'] ?? '';

// ID do usuário logado vindo da Session configurada no seu middleware
$idUsuarioLogado = $_SESSION['usuario']['id'] ?? 1; // Fallback para 1 caso utilize ambiente de testes sem session completa

switch ($acao) {
    case 'criar':
        $dados = [
            'id_eixo'            => intval($_POST['id_eixo'] ?? 0),
            'id_periodicidade'   => intval($_POST['id_periodicidade'] ?? 0),
            'id_publico_alvo'    => intval($_POST['id_publico_alvo'] ?? 0),
            'nome_projeto'       => trim($_POST['nome_projeto'] ?? ''),
            'objetivo'           => trim($_POST['objetivo'] ?? ''),
            'observacoes_gerais' => trim($_POST['observacoes_gerais'] ?? '') ?: null,
            'eh_publico'         => filter_var($_POST['eh_publico'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'url_imagem'         => trim($_POST['url_imagem'] ?? '') ?: null,
            'criado_por'         => $idUsuarioLogado,
            'atualizado_por'     => $idUsuarioLogado
        ];

        if (!$dados['id_eixo'] || !$dados['id_periodicidade'] || !$dados['id_publico_alvo'] || empty($dados['nome_projeto']) || empty($dados['objetivo'])) {
            respostaJSON(false, "Todos os campos obrigatórios devem ser preenchidos.");
        }

        if ($model->criar($dados)) {
            respostaJSON(true, "Atividade cadastrada com sucesso!");
        } else {
            respostaJSON(false, "Erro ao cadastrar atividade.");
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
        $dados = [
            'id'                 => intval($_POST['id'] ?? 0),
            'id_eixo'            => intval($_POST['id_eixo'] ?? 0),
            'id_periodicidade'   => intval($_POST['id_periodicidade'] ?? 0),
            'id_publico_alvo'    => intval($_POST['id_publico_alvo'] ?? 0),
            'nome_projeto'       => trim($_POST['nome_projeto'] ?? ''),
            'objetivo'           => trim($_POST['objetivo'] ?? ''),
            'observacoes_gerais' => trim($_POST['observacoes_gerais'] ?? '') ?: null,
            'eh_publico'         => filter_var($_POST['eh_publico'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'url_imagem'         => trim($_POST['url_imagem'] ?? '') ?: null,
            'atualizado_por'     => $idUsuarioLogado
        ];

        if ($dados['id'] <= 0 || !$dados['id_eixo'] || !$dados['id_periodicidade'] || !$dados['id_publico_alvo'] || empty($dados['nome_projeto']) || empty($dados['objective'])) {
            // Pequeno ajuste de validação de string vazia
            if(empty($dados['nome_projeto']) || empty($dados['objetivo'])) {
                respostaJSON(false, "Nome do projeto e objetivo não podem ficar vazios.");
            }
        }

        if ($model->atualizar($dados)) {
            respostaJSON(true, "Atividade atualizada com sucesso!");
        } else {
            respostaJSON(false, "Erro ao atualizar a atividade.");
        }
        break;

    case 'deletar':
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            respostaJSON(false, "ID inválido.");
        }

        if ($model->deletar($id)) {
            respostaJSON(true, "Atividade excluída com sucesso!");
        } else {
            respostaJSON(false, "Não foi possível excluir. Certifique-se de que não existem execuções desta atividade registradas.");
        }
        break;

    // ROTA AUXILIAR: Busca listas das outras tabelas para montar os <select> no HTML
    case 'auxiliares':
        $eixos = $conexao->query("SELECT id, nome_eixo FROM eixo")->fetch_all(MYSQLI_ASSOC);
        $periodos = $conexao->query("SELECT id, descricao FROM periodicidade")->fetch_all(MYSQLI_ASSOC);
        $publicos = $conexao->query("SELECT id, nome_publico FROM publico_alvo")->fetch_all(MYSQLI_ASSOC);
        
        respostaJSON(true, "Listas auxiliares carregadas.", [
            'eixos' => $eixos,
            'periodicidades' => $periodos,
            'publicos_alvo' => $publicos
        ]);
        break;

    default:
        respostaJSON(false, "Ação não permitida.");
        break;
}