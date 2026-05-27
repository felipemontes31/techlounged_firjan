<?php

class PublicoAlvo
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // CREATE
    public function criar($nome_publico)
    {
        $sql = "INSERT INTO publico_alvo (nome_publico) 
        VALUES (?)";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("s", $nome_publico);
        return $stmt->execute();
    }

    // READ ALL
    public function listarTodos()
    {
        $sql = "SELECT id, nome_publico 
        FROM publico_alvo 
        ORDER BY id DESC";
        $resultado = $this->conexao->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // READ BY ID
    public function buscarPorId($id)
    {
        $sql = "SELECT id, nome_publico 
        FROM publico_alvo 
        WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    // UPDATE
    public function atualizar($id, $nome_publico)
    {
        $sql = "UPDATE publico_alvo 
        SET nome_publico = ? 
        WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("si", $nome_publico, $id);
        return $stmt->execute();
    }

    // DELETE
    public function deletar($id)
    {
        $sql = "DELETE FROM publico_alvo 
        WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}