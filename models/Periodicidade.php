<?php

class Periodicidade
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // CREATE
    public function criar($descricao)
    {
        $sql = "INSERT INTO periodicidade (descricao) 
        VALUES (?)";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("s", $descricao);
        return $stmt->execute();
    }

    // READ ALL
    public function listarTodos()
    {
        $sql = "SELECT id, descricao 
        FROM periodicidade 
        ORDER BY id DESC";
        $resultado = $this->conexao->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // READ BY ID
    public function buscarPorId($id)
    {
        $sql = "SELECT id, descricao 
        FROM periodicidade 
        WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    // UPDATE
    public function atualizar($id, $descricao)
    {
        $sql = "UPDATE periodicidade 
        SET descricao = ? 
        WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("si", $descricao, $id);
        return $stmt->execute();
    }

    // DELETE
    public function deletar($id)
    {
        $sql = "DELETE FROM periodicidade 
        WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}