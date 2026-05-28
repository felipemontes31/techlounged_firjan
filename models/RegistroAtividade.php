<?php

class RegistroAtividade
{
    private mysqli $conexao;

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
    }

    private function executarSelect(string $sql, string $tipos = '', array $valores = []): array
    {
        $stmt = $this->conexao->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erro ao preparar consulta: " . $this->conexao->error);
        }

        if ($tipos !== '' && count($valores) > 0) {
            $stmt->bind_param($tipos, ...$valores);
        }

        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar consulta: " . $stmt->error);
        }

        $resultado = $stmt->get_result();

        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    private function montarCondicoesFiltros(array $filtros, string &$tipos, array &$valores): array
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

        if (!empty($filtros['id_registro'])) {
            $condicoes[] = "ra.id = ?";
            $tipos .= "i";
            $valores[] = intval($filtros['id_registro']);
        }

        if (!empty($filtros['busca'])) {
            $condicoes[] = "(
                a.nome_projeto LIKE ?
                OR COALESCE(ra.tema_especifico, '') LIKE ?
                OR e.nome_espaco LIKE ?
                OR COALESCE(a.objetivo, '') LIKE ?
            )";

            $busca = "%" . $filtros['busca'] . "%";

            $tipos .= "ssss";
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
        }

        return $condicoes;
    }

    private function sqlBaseEventos(): string
    {
        return "
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
                ra.criado_por,
                ra.atualizado_por,
                a.nome_projeto,
                a.objetivo,
                a.eh_publico,
                a.url_imagem AS url_imagem_atividade,
                COALESCE(NULLIF(ra.url_imagem, ''), NULLIF(a.url_imagem, '')) AS imagem_exibicao,
                e.nome_espaco,
                e.capacidade_maxima,
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
        ";
    }

    public function listarEventosPublicos(array $filtros = []): array
    {
        $tipos = "";
        $valores = [];

        $condicoes = $this->montarCondicoesFiltros($filtros, $tipos, $valores);
        $condicoes[] = "a.eh_publico = 1";
        $condicoes[] = "ra.data_execucao >= CURDATE()";

        $where = "WHERE " . implode(" AND ", $condicoes);

        $sql = $this->sqlBaseEventos() . "
            $where
            ORDER BY ra.data_execucao ASC, ra.id ASC
        ";

        return $this->executarSelect($sql, $tipos, $valores);
    }

    public function listarEventosAdmin(array $filtros = []): array
    {
        $tipos = "";
        $valores = [];

        $condicoes = $this->montarCondicoesFiltros($filtros, $tipos, $valores);
        $where = count($condicoes) > 0 ? "WHERE " . implode(" AND ", $condicoes) : "";

        $sql = $this->sqlBaseEventos() . "
            $where
            ORDER BY ra.data_execucao DESC, ra.id DESC
        ";

        return $this->executarSelect($sql, $tipos, $valores);
    }

    public function listarEventosComInscricaoDoUsuario(array $filtros, int $idUsuario): array
    {
        $tipos = "i";
        $valores = [$idUsuario];

        $condicoes = $this->montarCondicoesFiltros($filtros, $tipos, $valores);
        $condicoes[] = "ra.data_execucao >= CURDATE()";

        $where = "WHERE " . implode(" AND ", $condicoes);

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
                ra.criado_por,
                ra.atualizado_por,
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

        return $this->executarSelect($sql, $tipos, $valores);
    }

    public function listarHistoricoComum(array $filtros, int $idUsuario): array
    {
        $tipos = "i";
        $valores = [$idUsuario];

        $condicoes = $this->montarCondicoesFiltros($filtros, $tipos, $valores);
        $condicoes[] = "ra.data_execucao < CURDATE()";
        $condicoes[] = "EXISTS (
            SELECT 1
            FROM inscricao i
            WHERE i.id_registro_atividade = ra.id
            AND i.id_usuario_inscrito = ?
        )";

        $tipos .= "i";
        $valores[] = $idUsuario;

        $where = "WHERE " . implode(" AND ", $condicoes);

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
            INNER JOIN inscricao iu
                ON iu.id_registro_atividade = ra.id
                AND iu.id_usuario_inscrito = ?
            $where
            ORDER BY ra.data_execucao DESC, ra.id DESC
        ";

        return $this->executarSelect($sql, $tipos, $valores);
    }

    public function listarMinhasInscricoes(int $idUsuario): array
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
                e.capacidade_maxima,
                CASE
                    WHEN ra.data_execucao >= CURDATE()
                    AND ra.status = 'Planejado'
                    THEN 1
                    ELSE 0
                END AS pode_desinscrever
            FROM inscricao i
            INNER JOIN registro_atividade ra ON ra.id = i.id_registro_atividade
            INNER JOIN atividade a ON a.id = ra.id_atividade
            INNER JOIN espaco e ON e.id = ra.id_espaco
            WHERE i.id_usuario_inscrito = ?
            ORDER BY ra.data_execucao DESC, i.data_criacao DESC
        ";

        return $this->executarSelect($sql, "i", [$idUsuario]);
    }

    public function cancelarMinhaInscricaoFutura(int $idInscricao, int $idUsuario): bool
    {
        $sql = "
            DELETE i
            FROM inscricao i
            INNER JOIN registro_atividade ra ON ra.id = i.id_registro_atividade
            WHERE i.id = ?
            AND i.id_usuario_inscrito = ?
            AND ra.data_execucao >= CURDATE()
            AND ra.status = 'Planejado'
        ";

        $stmt = $this->conexao->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erro ao preparar cancelamento: " . $this->conexao->error);
        }

        $stmt->bind_param("ii", $idInscricao, $idUsuario);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao cancelar inscrição: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "
            SELECT
                ra.*,
                a.nome_projeto,
                a.objetivo,
                a.eh_publico,
                a.url_imagem AS url_imagem_atividade,
                COALESCE(NULLIF(ra.url_imagem, ''), NULLIF(a.url_imagem, '')) AS imagem_exibicao,
                e.nome_espaco,
                e.capacidade_maxima
            FROM registro_atividade ra
            INNER JOIN atividade a ON ra.id_atividade = a.id
            INNER JOIN espaco e ON ra.id_espaco = e.id
            WHERE ra.id = ?
        ";

        $resultado = $this->executarSelect($sql, "i", [$id]);

        return $resultado[0] ?? null;
    }

    public function criar(array $dados): bool
    {
        $sql = "
            INSERT INTO registro_atividade (
                id_atividade,
                id_espaco,
                data_execucao,
                data_finalizacao,
                tema_especifico,
                status,
                publico_realizado,
                publico_previsto,
                url_imagem,
                confirm_auto,
                criado_por,
                atualizado_por
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->conexao->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erro ao preparar cadastro: " . $this->conexao->error);
        }

        $stmt->bind_param(
            "iissssiisiii",
            $dados['id_atividade'],
            $dados['id_espaco'],
            $dados['data_execucao'],
            $dados['data_finalizacao'],
            $dados['tema_especifico'],
            $dados['status'],
            $dados['publico_realizado'],
            $dados['publico_previsto'],
            $dados['url_imagem'],
            $dados['confirm_auto'],
            $dados['criado_por'],
            $dados['atualizado_por']
        );

        return $stmt->execute();
    }

    public function atualizar(array $dados): bool
    {
        $sql = "
            UPDATE registro_atividade
            SET
                id_atividade = ?,
                id_espaco = ?,
                data_execucao = ?,
                data_finalizacao = ?,
                tema_especifico = ?,
                status = ?,
                publico_realizado = ?,
                publico_previsto = ?,
                url_imagem = ?,
                confirm_auto = ?,
                atualizado_por = ?
            WHERE id = ?
        ";

        $stmt = $this->conexao->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erro ao preparar atualização: " . $this->conexao->error);
        }

        $stmt->bind_param(
            "iissssiisiii",
            $dados['id_atividade'],
            $dados['id_espaco'],
            $dados['data_execucao'],
            $dados['data_finalizacao'],
            $dados['tema_especifico'],
            $dados['status'],
            $dados['publico_realizado'],
            $dados['publico_previsto'],
            $dados['url_imagem'],
            $dados['confirm_auto'],
            $dados['atualizado_por'],
            $dados['id']
        );

        return $stmt->execute();
    }

    public function deletar(int $id): bool
    {
        $sql = "DELETE FROM registro_atividade WHERE id = ?";

        $stmt = $this->conexao->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erro ao preparar exclusão: " . $this->conexao->error);
        }

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function listarSelectsCadastro(): array
    {
        $atividades = $this->executarSelect("
            SELECT id, nome_projeto
            FROM atividade
            ORDER BY nome_projeto ASC
        ");

        $espacos = $this->executarSelect("
            SELECT id, nome_espaco, capacidade_maxima
            FROM espaco
            ORDER BY nome_espaco ASC
        ");

        return [
            "atividades" => $atividades,
            "espacos" => $espacos
        ];
    }
}

?>
