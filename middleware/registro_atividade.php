<?php

require_once(__DIR__ . "/../config/conexao.php");

class RegistroAtividade
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // =========================================================
    // LISTAR
    // =========================================================

    public function listarTodos($usuarioLogado = false)
    {

        if ($usuarioLogado) {

            $sql = "
                SELECT 
                    ra.*,
                    a.nome_projeto,
                    a.eh_publico,
                    a.url_imagem AS url_imagem_atividade,
                    e.nome_espaco
                FROM registro_atividade ra
                INNER JOIN atividade a 
                    ON ra.id_atividade = a.id
                INNER JOIN espaco e
                    ON ra.id_espaco = e.id
                ORDER BY ra.data_execucao DESC
            ";

            return $this->conexao->query($sql);
        }

        $sql = "
            SELECT 
                ra.*,
                a.nome_projeto,
                a.url_imagem AS url_imagem_atividade,
                e.nome_espaco
            FROM registro_atividade ra
            INNER JOIN atividade a 
                ON ra.id_atividade = a.id
            INNER JOIN espaco e
                ON ra.id_espaco = e.id
            WHERE a.eh_publico = TRUE
            ORDER BY ra.data_execucao DESC
        ";

        return $this->conexao->query($sql);
    }

    // =========================================================
    // BUSCAR POR ID
    // =========================================================

    public function buscarPorId($id, $usuarioLogado = false)
    {

        if ($usuarioLogado) {

            $sql = "
                SELECT 
                    ra.*,
                    a.nome_projeto,
                    a.eh_publico,
                    a.url_imagem AS url_imagem_atividade,
                    e.nome_espaco
                FROM registro_atividade ra
                INNER JOIN atividade a 
                    ON ra.id_atividade = a.id
                INNER JOIN espaco e
                    ON ra.id_espaco = e.id
                WHERE ra.id = ?
            ";
        } else {

            $sql = "
                SELECT 
                    ra.*,
                    a.nome_projeto,
                    a.url_imagem AS url_imagem_atividade,
                    e.nome_espaco
                FROM registro_atividade ra
                INNER JOIN atividade a 
                    ON ra.id_atividade = a.id
                INNER JOIN espaco e
                    ON ra.id_espaco = e.id
                WHERE ra.id = ?
                AND a.eh_publico = TRUE
            ";
        }

        $stmt = $this->conexao->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt->get_result();
    }

    // =========================================================
    // CRIAR
    // =========================================================

    public function criar($dados)
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
                criado_por,
                atualizado_por
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bind_param(
            "iissssiisii",
            $dados['id_atividade'],
            $dados['id_espaco'],
            $dados['data_execucao'],
            $dados['data_finalizacao'],
            $dados['tema_especifico'],
            $dados['status'],
            $dados['publico_realizado'],
            $dados['publico_previsto'],
            $dados['url_imagem'],
            $dados['criado_por'],
            $dados['atualizado_por']
        );

        return $stmt->execute();
    }

    // =========================================================
    // EDITAR
    // =========================================================

    public function atualizar($id, $dados)
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
                atualizado_por = ?
            WHERE id = ?
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bind_param(
            "iissssiisii",
            $dados['id_atividade'],
            $dados['id_espaco'],
            $dados['data_execucao'],
            $dados['data_finalizacao'],
            $dados['tema_especifico'],
            $dados['status'],
            $dados['publico_realizado'],
            $dados['publico_previsto'],
            $dados['url_imagem'],
            $dados['atualizado_por'],
            $id
        );

        return $stmt->execute();
    }

    // =========================================================
    // EXCLUIR
    // =========================================================

    public function excluir($id)
    {

        $sql = "DELETE FROM registro_atividade WHERE id = ?";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}

?>