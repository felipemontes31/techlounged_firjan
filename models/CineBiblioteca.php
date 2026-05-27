<?php

class CineBiblioteca
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // CREATE
    public function criar($dados)
    {
        $sql = "
            INSERT INTO cine_biblioteca (
                id_registro_atividade, titulo_curta, link, 
                detalhes_controle, criado_por, atualizado_por
            ) VALUES (?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param(
            "isssii",
            $dados['id_registro_atividade'],
            $dados['titulo_curta'],
            $dados['link'],
            $dados['detalhes_controle'],
            $dados['criado_por'],
            $dados['atualizado_por']
        );
        return $stmt->execute();
    }

    // READ ALL com Filtros Dinâmicos
    public function listarTodos($filtros = [])
    {
        $sql = "
            SELECT 
                cb.*, 
                ra.tema_especifico, 
                ra.data_execucao,
                ra.data_finalizacao,
                ra.status,
                a.nome_projeto,
                u.nome AS nome_editor
            FROM cine_biblioteca cb
            INNER JOIN registro_atividade ra ON cb.id_registro_atividade = ra.id
            INNER JOIN atividade a ON ra.id_atividade = a.id
            INNER JOIN usuario u ON cb.atualizado_por = u.id
        ";

        // Construção dinâmica dos filtros WHERE
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

        $sql .= " ORDER BY cb.id DESC";

        $stmt = $this->conexao->prepare($sql);
        
        if (count($condicoes) > 0) {
            $stmt->bind_param($tipos, ...$valores);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // READ BY ID
    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM cine_biblioteca WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    // UPDATE
    public function atualizar($dados)
    {
        $sql = "
            UPDATE cine_biblioteca SET 
                id_registro_atividade = ?, 
                titulo_curta = ?, 
                link = ?, 
                detalhes_controle = ?, 
                atualizado_por = ?
            WHERE id = ?
        ";
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param(
            "isssii",
            $dados['id_registro_atividade'],
            $dados['titulo_curta'],
            $dados['link'],
            $dados['detalhes_controle'],
            $dados['atualizado_por'],
            $dados['id']
        );
        return $stmt->execute();
    }

    // DELETE
    public function deletar($id)
    {
        $sql = "DELETE FROM cine_biblioteca WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}