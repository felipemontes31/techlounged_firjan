<?php

class RegistroAtividade
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    public function criar($dados)
    {
        $sql = "INSERT INTO registro_atividade (id_atividade, id_espaco, data_execucao, data_finalizacao, tema_especifico, status, publico_realizado, publico_previsto, url_imagem, confirm_auto, criado_por, updated_por) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        // Nota: Ajustado para o nome da coluna correto do banco se necessário ou use atualizado_por
        $sql = "INSERT INTO registro_atividade (id_atividade, id_espaco, data_execucao, data_finalizacao, tema_especifico, status, publico_realizado, publico_previsto, url_imagem, confirm_auto, criado_por, atualizado_por) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param(
            "iissssiiisii",
            $dados['id_atividade'], $dados['id_espaco'], $dados['data_execucao'], $dados['data_finalizacao'],
            $dados['tema_especifico'], $dados['status'], $dados['publico_realizado'], $dados['publico_previsto'],
            $dados['url_imagem'], $dados['confirm_auto'], $dados['criado_por'], $dados['atualizado_por']
        );
        return $stmt->execute();
    }

    // Listagem com os filtros dinâmicos de Data e Status solicitados
    public function listarComFiltros($filtros = [], $apenasPublicosFuturos = false, $idUsuarioHistorico = null)
    {
        $sql = "
            SELECT 
                ra.*, 
                a.nome_projeto, a.eh_publico, a.objetivo,
                COALESCE(ra.url_imagem, a.url_imagem) AS imagem_exibicao,
                e.nome_espaco, e.capacidade_maxima,
                (SELECT COUNT(*) FROM inscricao i WHERE i.id_registro_atividade = ra.id AND i.tipo_inscricao = 'Confirmado' AND i.status_inscricao = 'Confirmado') AS total_confirmados,
                (SELECT COUNT(*) FROM inscricao i WHERE i.id_registro_atividade = ra.id AND i.tipo_inscricao = 'Pensando') AS total_pensando
            FROM registro_atividade ra
            INNER JOIN atividade a ON ra.id_atividade = a.id
            INNER JOIN espaco e ON ra.id_espaco = e.id
        ";

        $condicoes = [];
        $tipos = "";
        $valores = [];

        // Filtro para Visitante Deslogado (Apenas públicos e futuros)
        if ($apenasPublicosFuturos) {
            $condicoes[] = "a.eh_publico = 1 AND ra.data_execucao >= CURDATE()";
        }

        // Filtro de Histórico do Usuário Comum (Eventos passados que ele se inscreveu)
        if ($idUsuarioHistorico !== null) {
            $condicoes[] = "ra.data_execucao <= CURDATE() AND ra.id IN (SELECT id_registro_atividade FROM inscricao WHERE id_usuario_inscrito = ?)";
            $tipos .= "i";
            $valores[] = $idUsuarioHistorico;
        }

        // Filtros solicitados via interface
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

        $sql .= " ORDER BY ra.data_execucao ASC";

        $stmt = $this->conexao->prepare($sql);
        if (count($condicoes) > 0) {
            $stmt->bind_param($tipos, ...$valores);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT ra.*, a.eh_publico, e.capacidade_maxima FROM registro_atividade ra 
                INNER JOIN atividade a ON ra.id_atividade = a.id 
                INNER JOIN espaco e ON ra.id_espaco = e.id WHERE ra.id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function atualizar($dados)
    {
        $sql = "UPDATE registro_atividade SET id_atividade = ?, id_espaco = ?, data_execucao = ?, data_finalizacao = ?, tema_especifico = ?, status = ?, publico_realizado = ?, publico_previsto = ?, url_imagem = ?, confirm_auto = ?, atualizado_por = ? WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param(
            "iissssiiisii",
            $dados['id_atividade'], $dados['id_espaco'], $dados['data_execucao'], $dados['data_finalizacao'],
            $dados['tema_especifico'], $dados['status'], $dados['publico_realizado'], $dados['publico_previsto'],
            $dados['url_imagem'], $dados['confirm_auto'], $dados['atualizado_por'], $dados['id']
        );
        return $stmt->execute();
    }

    public function deletar($id)
    {
        $sql = "DELETE FROM registro_atividade WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}