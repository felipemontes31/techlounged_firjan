<?php

require_once(__DIR__ . "/../config/conexao.php");
//require_once(__DIR__ . "/../middleware/permissao.php");
require_once(__DIR__ . "/../utils/json.php");
require_once(__DIR__ . "/../utils/response.php");
require_once(__DIR__ . "/../models/CineBiblioteca.php");

// Proteção do middleware de autenticação
//verificarPermissao(); 

$model = new CineBiblioteca($conexao);
$acao = $_GET['acao'] ?? '';

// ID do utilizador autenticado
$idUsuarioLogado = $_SESSION['usuario']['id'] ?? 1;

// Captura filtros globais enviados por GET da View
$filtros = [
    'data_execucao'   => $_GET['f_data_execucao'] ?? '',
    'data_finalizacao' => $_GET['f_data_finalizacao'] ?? '',
    'status'           => $_GET['f_status'] ?? ''
];

switch ($acao) {
    case 'criar':
        $dados = [
            'id_registro_atividade' => intval($_POST['id_registro_atividade'] ?? 0),
            'titulo_curta'          => trim($_POST['titulo_curta'] ?? ''),
            'link'                  => trim($_POST['link'] ?? '') ?: null,
            'detalhes_controle'     => trim($_POST['detalhes_controle'] ?? '') ?: null,
            'criado_por'            => $idUsuarioLogado,
            'atualizado_por'        => $idUsuarioLogado
        ];

        if (!$dados['id_registro_atividade'] || empty($dados['titulo_curta'])) {
            respostaJSON(false, "O registro de atividade e o título do curta são campos obrigatórios.");
        }

        if ($model->criar($dados)) {
            respostaJSON(true, "Mídia adicionada com sucesso ao Cine Biblioteca!");
        } else {
            respostaJSON(false, "Erro ao adicionar registro.");
        }
        break;

    case 'listar':
        // Passa o array de filtros capturados para a model
        $dados = $model->listarTodos($filtros);
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
            'id'                    => intval($_POST['id'] ?? 0),
            'id_registro_atividade' => intval($_POST['id_registro_atividade'] ?? 0),
            'titulo_curta'          => trim($_POST['titulo_curta'] ?? ''),
            'link'                  => trim($_POST['link'] ?? '') ?: null,
            'detalhes_controle'     => trim($_POST['detalhes_controle'] ?? '') ?: null,
            'atualizado_por'        => $idUsuarioLogado
        ];

        if ($dados['id'] <= 0 || !$dados['id_registro_atividade'] || empty($dados['titulo_curta'])) {
            respostaJSON(false, "Preencha todos os campos obrigatórios para atualização.");
        }

        if ($model->atualizar($dados)) {
            respostaJSON(true, "Mídia atualizada com sucesso!");
        } else {
            respostaJSON(false, "Erro ao atualizar o registro.");
        }
        break;

    case 'deletar':
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            respostaJSON(false, "ID inválido.");
        }

        if ($model->deletar($id)) {
            respostaJSON(true, "Registro removido com sucesso.");
        } else {
            respostaJSON(false, "Erro ao remover o registro.");
        }
        break;

    // ROTA AUXILIAR: Agora também filtra as opções do Select!
    case 'auxiliares':
        $sql = "
            SELECT ra.id, ra.tema_especifico, ra.data_execucao, ra.data_finalizacao, ra.status, a.nome_projeto 
            FROM registro_atividade ra
            INNER JOIN atividade a ON ra.id_atividade = a.id
        ";

        $condicoes = [];
        $tipos = "";
        $valores = [];

        if (!empty($filtros['data_execucao'])) {
            $condicoes[] = "ra.data_execucao = ?";
            $tipos .= "s";
            $valores[] = $filtros['data_execucao'];
        }
        if (!empty($filtros['data_finalizacao'])) {
            $condicoes[] = "ra.data_finalizacao = ?";
            $tipos .= "s";
            $valores[] = $filtros['data_finalizacao'];
        }
        if (!empty($filtros['status'])) {
            $condicoes[] = "ra.status = ?";
            $tipos .= "s";
            $valores[] = $filtros['status'];
        }

        if (count($condicoes) > 0) {
            $sql .= " WHERE " . implode(" AND ", $condicoes);
        }

        $sql .= " ORDER BY ra.data_execucao DESC";
        
        $stmt = $conexao->prepare($sql);
        if (count($condicoes) > 0) {
            $stmt->bind_param($tipos, ...$valores);
        }
        $stmt->execute();
        $registros = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        respostaJSON(true, "Registros carregados.", ['registros_atividades' => $registros]);
        break;

    default:
        respostaJSON(false, "Ação não permitida.");
        break;
}