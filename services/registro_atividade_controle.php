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

$usuarioSessao = $_SESSION['usuario'] ?? null;
$funcaoUsuario = $usuarioSessao['funcao'] ?? 'Visitante';
$idUsuarioLogado = isset($usuarioSessao['id']) ? intval($usuarioSessao['id']) : null;

$filtros = [
    'data_execucao'    => $_GET['f_data_execucao'] ?? '',
    'data_finalizacao' => $_GET['f_data_finalizacao'] ?? '',
    'status'           => $_GET['f_status'] ?? '',
    'busca'            => $_GET['f_busca'] ?? '',
    'id_registro'      => intval($_GET['f_id_registro'] ?? 0)
];

function usuarioEhGestorEvento(string $funcaoUsuario): bool
{
    return in_array($funcaoUsuario, ['Administrador', 'Bibliotecário'], true);
}

function exigirLogin(?int $idUsuarioLogado): void
{
    if (!$idUsuarioLogado) {
        respostaJSON(false, "Não autorizado.");
    }
}

function exigirGestorEvento(string $funcaoUsuario): void
{
    if (!usuarioEhGestorEvento($funcaoUsuario)) {
        respostaJSON(false, "Acesso restrito à administração.");
    }
}

function normalizarDadosRegistro(array $post, ?int $idUsuarioLogado, bool $incluiId = false): array
{
    $dados = [
        'id_atividade'      => intval($post['id_atividade'] ?? 0),
        'id_espaco'         => intval($post['id_espaco'] ?? 0),
        'data_execucao'     => trim($post['data_execucao'] ?? ''),
        'data_finalizacao'  => trim($post['data_finalizacao'] ?? '') ?: null,
        'tema_especifico'   => trim($post['tema_especifico'] ?? '') ?: null,
        'status'            => trim($post['status'] ?? 'Planejado'),
        'publico_previsto'  => intval($post['publico_previsto'] ?? 0) ?: null,
        'publico_realizado' => intval($post['publico_realizado'] ?? 0),
        'url_imagem'        => trim($post['url_imagem'] ?? '') ?: null,
        'confirm_auto'      => isset($post['confirm_auto']) ? 1 : 0,
        'atualizado_por'    => $idUsuarioLogado ?? 1
    ];

    if ($incluiId) {
        $dados['id'] = intval($post['id'] ?? 0);
    } else {
        $dados['criado_por'] = $idUsuarioLogado ?? 1;
    }

    return $dados;
}

try {
    switch ($acao) {
        // ----------------------------------------------------------------
        // ROTAS DE VISUALIZAÇÃO / FILTRO
        // ----------------------------------------------------------------
        case 'listar_publico':
            $dados = $modelRegistro->listarEventosPublicos($filtros);
            respostaJSON(true, "Eventos abertos carregados.", $dados);
            break;

        case 'listar_disponiveis_comum':
            exigirLogin($idUsuarioLogado);

            $dados = $modelRegistro->listarEventosComInscricaoDoUsuario($filtros, $idUsuarioLogado);
            respostaJSON(true, "Eventos ativos carregados.", $dados);
            break;

        case 'listar_historico_comum':
            exigirLogin($idUsuarioLogado);

            $dados = $modelRegistro->listarHistoricoComum($filtros, $idUsuarioLogado);
            respostaJSON(true, "Histórico carregado.", $dados);
            break;

        case 'minhas_inscricoes':
            exigirLogin($idUsuarioLogado);

            $dados = $modelRegistro->listarMinhasInscricoes($idUsuarioLogado);
            respostaJSON(true, "Minhas inscrições carregadas.", $dados);
            break;

        case 'listar_admin':
            exigirGestorEvento($funcaoUsuario);

            $dados = $modelRegistro->listarEventosAdmin($filtros);
            respostaJSON(true, "Painel administrativo carregado.", $dados);
            break;

        // ----------------------------------------------------------------
        // ROTAS DE INSCRIÇÃO
        // ----------------------------------------------------------------
        case 'inscrever':
            if (!$idUsuarioLogado) {
                respostaJSON(false, "login_obrigatorio");
            }

            $idRegistro = intval($_POST['id_registro_atividade'] ?? 0);
            $tipoInscricao = $_POST['tipo_inscricao'] ?? 'Confirmado';

            if ($idRegistro <= 0) {
                respostaJSON(false, "Evento inválido.");
            }

            if (!in_array($tipoInscricao, ['Confirmado', 'Pensando'], true)) {
                respostaJSON(false, "Tipo de inscrição inválido.");
            }

            $resultado = $modelInscricao->realizarInscricao($idRegistro, $idUsuarioLogado, $tipoInscricao);
            respostaJSON($resultado['sucesso'], $resultado['mensagem']);
            break;

        case 'alterar_inscricao_comum':
            exigirLogin($idUsuarioLogado);

            $idInscricao = intval($_POST['id_inscricao'] ?? 0);
            $novoTipo = $_POST['tipo_inscricao'] ?? 'Pensando';

            if (!in_array($novoTipo, ['Confirmado', 'Pensando'], true)) {
                respostaJSON(false, "Tipo de inscrição inválido.");
            }

            if ($modelInscricao->alterarMinhaInscricao($idInscricao, $idUsuarioLogado, $novoTipo)) {
                respostaJSON(true, "Inscrição modificada! O status retornou para Pendente de avaliação.");
            }

            respostaJSON(false, "Erro ao alterar inscrição.");
            break;

        case 'desinscrever':
            exigirLogin($idUsuarioLogado);

            $idInscricao = intval($_POST['id_inscricao'] ?? 0);

            if ($idInscricao <= 0) {
                respostaJSON(false, "Inscrição inválida.");
            }

            $cancelou = $modelRegistro->cancelarMinhaInscricaoFutura($idInscricao, $idUsuarioLogado);

            if ($cancelou) {
                respostaJSON(true, "Inscrição cancelada com sucesso.");
            }

            respostaJSON(false, "Não foi possível cancelar. O evento pode já ter ocorrido ou não estar mais planejado.");
            break;

        // ----------------------------------------------------------------
        // ROTAS DE GERENCIAMENTO DE INSCRITOS
        // ----------------------------------------------------------------
        case 'listar_inscritos_evento':
            exigirGestorEvento($funcaoUsuario);

            $idRegistro = intval($_GET['id_registro'] ?? 0);

            if ($idRegistro <= 0) {
                respostaJSON(false, "Evento inválido.");
            }

            $dados = $modelInscricao->listarPorEvento($idRegistro);
            respostaJSON(true, "Lista de inscritos obtida.", $dados);
            break;

        case 'modificar_status_admin':
            exigirGestorEvento($funcaoUsuario);

            $idInscricao = intval($_POST['id_inscricao'] ?? 0);
            $novoStatus = trim($_POST['status_inscricao'] ?? '');

            if ($idInscricao <= 0 || !in_array($novoStatus, ['Confirmado', 'Pendente', 'Recusada'], true)) {
                respostaJSON(false, "Parâmetros de alteração inválidos.");
            }

            if ($modelInscricao->atualizarStatusAdmin($idInscricao, $novoStatus)) {
                respostaJSON(true, "Status da inscrição atualizado para '{$novoStatus}' com sucesso!");
            }

            respostaJSON(false, "Erro ao atualizar o status da inscrição.");
            break;

        // ----------------------------------------------------------------
        // CRUD DE REGISTRO_ATIVIDADE
        // ----------------------------------------------------------------
        case 'criar':
            exigirGestorEvento($funcaoUsuario);

            $dados = normalizarDadosRegistro($_POST, $idUsuarioLogado, false);

            if (!$dados['id_atividade'] || !$dados['id_espaco'] || empty($dados['data_execucao'])) {
                respostaJSON(false, "Preencha a Atividade, o Espaço e a Data de Execução.");
            }

            if ($modelRegistro->criar($dados)) {
                respostaJSON(true, "Novo registro de atividade cadastrado com sucesso!");
            }

            respostaJSON(false, "Erro interno ao salvar o registro.");
            break;

        case 'buscar':
            exigirGestorEvento($funcaoUsuario);

            $id = intval($_GET['id'] ?? 0);

            if ($id <= 0) {
                respostaJSON(false, "ID inválido.");
            }

            $registro = $modelRegistro->buscarPorId($id);

            if ($registro) {
                respostaJSON(true, "Dados encontrados.", $registro);
            }

            respostaJSON(false, "Registro de atividade não encontrado.");
            break;

        case 'atualizar':
            exigirGestorEvento($funcaoUsuario);

            $dados = normalizarDadosRegistro($_POST, $idUsuarioLogado, true);

            if ($dados['id'] <= 0 || !$dados['id_atividade'] || !$dados['id_espaco'] || empty($dados['data_execucao'])) {
                respostaJSON(false, "Dados insuficientes ou inválidos para atualização.");
            }

            if ($modelRegistro->atualizar($dados)) {
                respostaJSON(true, "Registro de atividade atualizado com sucesso!");
            }

            respostaJSON(false, "Erro ao atualizar os dados do evento.");
            break;

        case 'deletar':
            exigirGestorEvento($funcaoUsuario);

            $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

            if ($id <= 0) {
                respostaJSON(false, "ID inválido.");
            }

            if ($modelRegistro->deletar($id)) {
                respostaJSON(true, "Registro de atividade excluído com sucesso.");
            }

            respostaJSON(false, "Erro ao excluir. Verifique se existem inscrições vinculadas.");
            break;

        case 'carregar_selects_cadastro':
            exigirGestorEvento($funcaoUsuario);

            $dados = $modelRegistro->listarSelectsCadastro();
            respostaJSON(true, "Dados auxiliares carregados.", $dados);
            break;

        default:
            respostaJSON(false, "Ação desconhecida.");
            break;
    }
} catch (Throwable $erro) {
    respostaJSON(false, "Erro interno: " . $erro->getMessage());
}

?>
