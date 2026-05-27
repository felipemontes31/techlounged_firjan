<?php

class Eixo
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // CREATE
    public function criar($nome_eixo, $observacao)
    {
        $sql = "INSERT INTO eixo (nome_eixo, observacao) VALUES (?, ?)";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ss", $nome_eixo, $observacao);
        return $stmt->execute();
    }

    // READ ALL
    public function listarTodos()
    {
        $sql = "SELECT id, nome_eixo, observacao FROM eixo ORDER BY id DESC";
        $resultado = $this->conexao->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // READ BY ID
    public function buscarPorId($id)
    {
        $sql = "SELECT id, nome_eixo, observacao FROM eixo WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    // UPDATE
    public function atualizar($id, $nome_eixo, $observacao)
    {
        $sql = "UPDATE eixo SET nome_eixo = ?, observacao = ? WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ssi", $nome_eixo, $observacao, $id);
        return $stmt->execute();
    }

    // DELETE
    public function deletar($id)
    {
        $sql = "DELETE FROM eixo WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}