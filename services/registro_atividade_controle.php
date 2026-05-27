<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . "/../config/conexao.php");
require_once(__DIR__ . "/../utils/json.php");
require_once(__DIR__ . "/../models/RegistroAtividade.php");
require_once(__DIR__ . "/../models/Inscricao.php");

$modelRegistro = new RegistroAtividade($conexao);
$modelInscricao = new Inscricao($conexao);

$acao = $_GET['acao'] ?? '';
$funcaoUsuario = $_SESSION['usuario']['funcao'] ?? 'Visitante';
$idUsuarioLogado = $_SESSION['usuario']['id'] ?? null;

// Filtros recebidos via GET
$filtros = [
    'data_execucao'   => $_GET['f_data_execucao'] ?? '',
    'data_finalizacao' => $_GET['f_data_finalizacao'] ?? '',
    'status'           => $_GET['f_status'] ?? ''
];

switch ($acao) {
    // ----------------------------------------------------------------
    // ROTAS DE VISUALIZAÇÃO / FILTRO (Público, Comum e Admin)
    // ----------------------------------------------------------------
    case 'listar_publico':
        // Visitantes deslogados: Apenas futuros e públicos (eh_publico = 1)
        $dados = $modelRegistro->listarComFiltros($filtros, true);
        respostaJSON(true, "Eventos abertos carregados.", $dados);
        break;

    case 'listar_disponiveis_comum':
        if (!$idUsuarioLogado) respostaJSON(false, "Não autorizado.");
        // Usuário Comum logado: Vê eventos futuros (>= hoje) independente de eh_publico
        // Criamos a lógica direto filtrando por data futura na consulta
        $eventos = $modelRegistro->listarComFiltros($filtros);
        $futuros = array_values(array_filter($eventos, function($e) {
            return strtotime($e['data_execucao']) >= strtotime(date('Y-m-d'));
        }));
        respostaJSON(true, "Eventos ativos carregados.", $futuros);
        break;

    case 'listar_historico_comum':
        if (!$idUsuarioLogado) respostaJSON(false, "Não autorizado.");
        // Histórico do usuário comum: Passados onde ele se inscreveu
        $dados = $modelRegistro->listarComFiltros($filtros, false, $idUsuarioLogado);
        respostaJSON(true, "Histórico carregado.", $dados);
        break;

    case 'listar_admin':
        // Admin e Bibliotecários: Visualizam tudo e gerenciam contadores
        if (!in_array($funcaoUsuario, ['Administrador', 'Bibliotecário'])) {
            respostaJSON(false, "Acesso restrito à administração.");
        }
        $dados = $modelRegistro->listarComFiltros($filtros);
        respostaJSON(true, "Painel administrativo carregado.", $dados);
        break;

    // ----------------------------------------------------------------
    // ROTAS DE INSCRIÇÃO
    // ----------------------------------------------------------------
    case 'inscrever':
        if (!$idUsuarioLogado) {
            respostaJSON(false, "login_obrigatorio"); // Avisa o JS para redirecionar
        }
        $id_registro = intval($_POST['id_registro_atividade'] ?? 0);
        $tipo_inscricao = $_POST['tipo_inscricao'] ?? 'Confirmado'; // 'Confirmado' ou 'Pensando'

        $resultado = $modelInscricao->realizarInscricao($id_registro, $idUsuarioLogado, $tipo_inscricao);
        respostaJSON($resultado['sucesso'], $resultado['mensagem']);
        break;

    case 'alterar_inscricao_comum':
        if (!$idUsuarioLogado) respostaJSON(false, "Não autorizado.");
        $id_inscricao = intval($_POST['id_inscricao'] ?? 0);
        $novo_tipo = $_POST['tipo_inscricao'] ?? 'Pensando';

        if ($modelInscricao->alterarMinhaInscricao($id_inscricao, $idUsuarioLogado, $novo_tipo)) {
            respostaJSON(true, "Inscrição modificada! O status retornou para Pendente de avaliação.");
        } else {
            respostaJSON(false, "Erro ao alterar inscrição.");
        }
        break;

    // ----------------------------------------------------------------
    // ROTAS DE GERENCIAMENTO (EXCLUSIVO ADMIN / BIBLIOTECÁRIO)
    // ----------------------------------------------------------------
    case 'listar_inscritos_evento':
        if (!in_array($funcaoUsuario, ['Administrador', 'Bibliotecário'])) respostaJSON(false, "Negado.");
        $id_registro = intval($_GET['id_registro'] ?? 0);
        $dados = $modelInscricao->listarPorEvento($id_registro);
        respostaJSON(true, "Lista de inscritos obtida.", $dados);
        break;

    case 'modificar_status_admin':
        if (!in_array($funcaoUsuario, ['Administrador', 'Bibliotecário'])) respostaJSON(false, "Negado.");
        $id_inscricao = intval($_POST['id_inscricao'] ?? 0);
        $novo_status = $_POST['status_inscricao'] ?? 'Pendente'; // Confirmado, Pendente, Recusada

        if ($modelInscricao->atualizarStatusAdmin($id_inscricao, $novo_status)) {
            respostaJSON(true, "Status da inscrição alterado com sucesso!");
        } else {
            respostaJSON(false, "Erro ao atualizar status.");
        }
        break;

    default:
        respostaJSON(false, "Ação desconhecida.");
        break;
}