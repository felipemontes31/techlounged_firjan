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
    'data_execucao'    => $_GET['f_data_execucao'] ?? '',
    'data_finalizacao' => $_GET['f_data_finalizacao'] ?? '',
    'status'           => $_GET['f_status'] ?? ''
];

function executarSelectRegistro(mysqli $conexao, string $sql, string $tipos = '', array $valores = []): array
{
    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        respostaJSON(false, "Erro ao preparar consulta: " . $conexao->error);
    }

    if ($tipos !== '' && count($valores) > 0) {
        $referencias = [];
        foreach ($valores as $chave => $valor) {
            $referencias[$chave] = &$valores[$chave];
        }
        array_unshift($referencias, $tipos);
        call_user_func_array([$stmt, 'bind_param'], $referencias);
    }

    if (!$stmt->execute()) {
        respostaJSON(false, "Erro ao executar consulta: " . $stmt->error);
    }

    $resultado = $stmt->get_result();
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

function montarCondicoesFiltros(array $filtros, string &$tipos, array &$valores): array
{
    $condicoes = [];

    if (!empty($filtros['data_execucao'])) {
        $condicoes[] = "ra.data_execucao >= ?";
        $tipos .= "s";
        $valores[] = $filtros['data_execucao'];
    }

    if (!empty($filtros['data_finalizacao'])) {
        $condicoes[] = "COALESCE(ra.data_finalizacao, ra.data_execucao) <= ?";
        $tipos .= "s";
        $valores[] = $filtros['data_finalizacao'];
    }

    if (!empty($filtros['status'])) {
        $condicoes[] = "ra.status = ?";
        $tipos .= "s";
        $valores[] = $filtros['status'];
    }

    return $condicoes;
}

function listarEventosComInscricaoDoUsuario(mysqli $conexao, array $filtros, int $idUsuario): array
{
    $tipos = "i";
    $valores = [$idUsuario];

    $condicoes = montarCondicoesFiltros($filtros, $tipos, $valores);
    $condicoes[] = "ra.data_execucao >= CURDATE()";

    $where = count($condicoes) ? "WHERE " . implode(" AND ", $condicoes) : "";

    $sql = "
        SELECT
            ra.id,
            ra.id_atividade,
            ra.id_espaco,
            ra.data_execucao,
            ra.data_finalizacao,
            ra.tema_especifico,
            ra.status,
            ra.publico_realizado,
            ra.publico_previsto,
            ra.url_imagem,
            ra.confirm_auto,
            a.nome_projeto,
            a.objetivo,
            a.eh_publico,
            a.url_imagem AS url_imagem_atividade,
            COALESCE(NULLIF(ra.url_imagem, ''), NULLIF(a.url_imagem, '')) AS imagem_exibicao,
            e.nome_espaco,
            e.capacidade_maxima,
            iu.id AS id_inscricao_usuario,
            iu.tipo_inscricao AS minha_tipo_inscricao,
            iu.status_inscricao AS minha_status_inscricao,
            (
                SELECT COUNT(*)
                FROM inscricao ic
                WHERE ic.id_registro_atividade = ra.id
                  AND ic.status_inscricao = 'Confirmado'
            ) AS total_confirmados,
            (
                SELECT COUNT(*)
                FROM inscricao ip
                WHERE ip.id_registro_atividade = ra.id
                  AND ip.tipo_inscricao = 'Pensando'
            ) AS total_pensando
        FROM registro_atividade ra
        INNER JOIN atividade a ON a.id = ra.id_atividade
        INNER JOIN espaco e ON e.id = ra.id_espaco
        LEFT JOIN inscricao iu
               ON iu.id_registro_atividade = ra.id
              AND iu.id_usuario_inscrito = ?
        $where
        ORDER BY ra.data_execucao ASC, ra.id ASC
    ";

    return executarSelectRegistro($conexao, $sql, $tipos, $valores);
}

function listarMinhasInscricoes(mysqli $conexao, int $idUsuario): array
{
    $sql = "
        SELECT
            i.id AS id_inscricao,
            i.tipo_inscricao,
            i.status_inscricao,
            i.data_criacao AS data_inscricao,
            ra.id AS id_registro_atividade,
            ra.data_execucao,
            ra.data_finalizacao,
            ra.tema_especifico,
            ra.status AS status_evento,
            ra.publico_previsto,
            ra.url_imagem,
            a.nome_projeto,
            a.objetivo,
            a.eh_publico,
            a.url_imagem AS url_imagem_atividade,
            COALESCE(NULLIF(ra.url_imagem, ''), NULLIF(a.url_imagem, '')) AS imagem_exibicao,
            e.nome_espaco,
            e.capacidade_maxima
        FROM inscricao i
        INNER JOIN registro_atividade ra ON ra.id = i.id_registro_atividade
        INNER JOIN atividade a ON a.id = ra.id_atividade
        INNER JOIN espaco e ON e.id = ra.id_espaco
        WHERE i.id_usuario_inscrito = ?
        ORDER BY ra.data_execucao DESC, i.data_criacao DESC
    ";

    return executarSelectRegistro($conexao, $sql, "i", [$idUsuario]);
}

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

        // Retorna os eventos futuros já com os dados da inscrição do usuário logado.
        // Isso permite bloquear o botão quando ele já estiver inscrito.
        $dados = listarEventosComInscricaoDoUsuario($conexao, $filtros, intval($idUsuarioLogado));
        respostaJSON(true, "Eventos ativos carregados.", $dados);
        break;

    case 'listar_historico_comum':
        if (!$idUsuarioLogado) respostaJSON(false, "Não autorizado.");
        $dados = $modelRegistro->listarComFiltros($filtros, false, $idUsuarioLogado);
        respostaJSON(true, "Histórico carregado.", $dados);
        break;

    case 'minhas_inscricoes':
        if (!$idUsuarioLogado) respostaJSON(false, "Não autorizado.");
        $dados = listarMinhasInscricoes($conexao, intval($idUsuarioLogado));
        respostaJSON(true, "Minhas inscrições carregadas.", $dados);
        break;

    case 'listar_admin':
        // Admin e Bibliotecários: Visualizam tudo e gerenciam contadores
        if (!in_array($funcaoUsuario, ['Administrador', 'Bibliotecário', 'Bibliotecario'])) {
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
            respostaJSON(false, "login_obrigatorio");
        }
        $id_registro = intval($_POST['id_registro_atividade'] ?? 0);
        $tipo_inscricao = $_POST['tipo_inscricao'] ?? 'Confirmado';

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
        if (!in_array($funcaoUsuario, ['Administrador', 'Bibliotecário', 'Bibliotecario'])) respostaJSON(false, "Negado.");
        $id_registro = intval($_GET['id_registro'] ?? 0);
        $dados = $modelInscricao->listarPorEvento($id_registro);
        respostaJSON(true, "Lista de inscritos obtida.", $dados);
        break;

    case 'modificar_status_admin':
        if (!in_array($funcaoUsuario, ['Administrador', 'Bibliotecário', 'Bibliotecario'])) {
            respostaJSON(false, "Acesso negado.");
        }

        $id_inscricao = intval($_POST['id_inscricao'] ?? 0);
        $novo_status = trim($_POST['status_inscricao'] ?? '');

        if ($id_inscricao <= 0 || !in_array($novo_status, ['Confirmado', 'Pendente', 'Recusada'])) {
            respostaJSON(false, "Parâmetros de alteração inválidos.");
        }

        if ($modelInscricao->atualizarStatusAdmin($id_inscricao, $novo_status)) {
            respostaJSON(true, "Status da inscrição atualizado para '{$novo_status}' com sucesso!");
        } else {
            respostaJSON(false, "Erro ao atualizar o status da inscrição no banco de dados.");
        }
        break;

    // ----------------------------------------------------------------
    // CRUD DE GERENCIAMENTO DO REGISTRO_ATIVIDADE
    // ----------------------------------------------------------------
    case 'criar':
        if (!in_array($funcaoUsuario, ['Administrador', 'Bibliotecário', 'Bibliotecario'])) {
            respostaJSON(false, "Acesso negado para esta operação.");
        }

        $dados = [
            'id_atividade'      => intval($_POST['id_atividade'] ?? 0),
            'id_espaco'         => intval($_POST['id_espaco'] ?? 0),
            'data_execucao'     => trim($_POST['data_execucao'] ?? ''),
            'data_finalizacao'  => trim($_POST['data_finalizacao'] ?? '') ?: null,
            'tema_especifico'   => trim($_POST['tema_especifico'] ?? '') ?: null,
            'status'            => trim($_POST['status'] ?? 'Planejado'),
            'publico_previsto'  => intval($_POST['publico_previsto'] ?? 0) ?: null,
            'publico_realizado' => intval($_POST['publico_realizado'] ?? 0),
            'url_imagem'        => trim($_POST['url_imagem'] ?? '') ?: null,
            'confirm_auto'      => isset($_POST['confirm_auto']) ? 1 : 0,
            'criado_por'        => $idUsuarioLogado ?? 1,
            'atualizado_por'    => $idUsuarioLogado ?? 1
        ];

        if (!$dados['id_atividade'] || !$dados['id_espaco'] || empty($dados['data_execucao'])) {
            respostaJSON(false, "Por favor, preencha a Atividade, o Espaço e a Data de Execução.");
        }

        if ($modelRegistro->criar($dados)) {
            respostaJSON(true, "Novo registro de atividade cadastrado com sucesso!");
        } else {
            respostaJSON(false, "Erro interno ao salvar o registro no banco de dados.");
        }
        break;

    case 'buscar':
        if (!in_array($funcaoUsuario, ['Administrador', 'Bibliotecário', 'Bibliotecario'])) {
            respostaJSON(false, "Acesso negado.");
        }

        $id = intval($_GET['id'] ?? 0);
        $registro = $modelRegistro->buscarPorId($id);

        if ($registro) {
            respostaJSON(true, "Dados encontrados.", $registro);
        } else {
            respostaJSON(false, "Registro de atividade não encontrado.");
        }
        break;

    case 'atualizar':
        if (!in_array($funcaoUsuario, ['Administrador', 'Bibliotecário', 'Bibliotecario'])) {
            respostaJSON(false, "Acesso negado.");
        }

        $dados = [
            'id'                => intval($_POST['id'] ?? 0),
            'id_atividade'      => intval($_POST['id_atividade'] ?? 0),
            'id_espaco'         => intval($_POST['id_espaco'] ?? 0),
            'data_execucao'     => trim($_POST['data_execucao'] ?? ''),
            'data_finalizacao'  => trim($_POST['data_finalizacao'] ?? '') ?: null,
            'tema_especifico'   => trim($_POST['tema_especifico'] ?? '') ?: null,
            'status'            => trim($_POST['status'] ?? 'Planejado'),
            'publico_previsto'  => intval($_POST['publico_previsto'] ?? 0) ?: null,
            'publico_realizado' => intval($_POST['publico_realizado'] ?? 0),
            'url_imagem'        => trim($_POST['url_imagem'] ?? '') ?: null,
            'confirm_auto'      => isset($_POST['confirm_auto']) ? 1 : 0,
            'atualizado_por'    => $idUsuarioLogado ?? 1
        ];

        if ($dados['id'] <= 0 || !$dados['id_atividade'] || !$dados['id_espaco'] || empty($dados['data_execucao'])) {
            respostaJSON(false, "Dados insuficientes ou inválidos para atualização.");
        }

        if ($modelRegistro->atualizar($dados)) {
            respostaJSON(true, "Registro de atividade atualizado com sucesso!");
        } else {
            respostaJSON(false, "Erro ao atualizar os dados do evento.");
        }
        break;

    case 'carregar_selects_cadastro':
        if (!in_array($funcaoUsuario, ['Administrador', 'Bibliotecário', 'Bibliotecario'])) {
            respostaJSON(false, "Acesso negado.");
        }

        $sqlAtividades = "SELECT id, nome_projeto FROM atividade ORDER BY nome_projeto ASC";
        $atividades = $conexao->query($sqlAtividades)->fetch_all(MYSQLI_ASSOC);

        $sqlEspacos = "SELECT id, nome_espaco, capacidade_maxima FROM espaco ORDER BY nome_espaco ASC";
        $espacos = $conexao->query($sqlEspacos)->fetch_all(MYSQLI_ASSOC);

        respostaJSON(true, "Dados auxiliares carregados.", [
            'atividades' => $atividades,
            'espacos'    => $espacos
        ]);
        break;

    default:
        respostaJSON(false, "Ação desconhecida.");
        break;
}
