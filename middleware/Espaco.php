<?php

class Espaco
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // CREATE
    public function criar($nome_espaco, $capacidade_maxima)
    {
        $sql = "INSERT INTO espaco (nome_espaco, capacidade_maxima) 
        VALUES (?, ?)";
        $stmt = $this->conexao->prepare($sql);
        // "si" -> string (nome_espaco), integer (capacidade_maxima)
        $stmt->bind_param(
            "si", 
            $nome_espaco, 
            $capacidade_maxima
            );
        return $stmt->execute();
    }

    // READ ALL
    public function listarTodos()
    {
        $sql = "SELECT id, nome_espaco, capacidade_maxima 
        FROM espaco 
        ORDER BY id DESC";
        $resultado = $this->conexao->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // READ BY ID
    public function buscarPorId($id)
    {
        $sql = "SELECT id, nome_espaco, capacidade_maxima 
        FROM espaco 
        WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    // UPDATE
    public function atualizar($id, $nome_espaco, $capacidade_maxima)
    {
        $sql = "UPDATE espaco 
        SET nome_espaco = ?, capacidade_maxima = ? 
        WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param(
            "sii", 
            $nome_espaco, 
            $capacidade_maxima, 
            $id
            );

        return $stmt->execute();
    }

    // DELETE
    public function deletar($id)
    {
        $sql = "DELETE FROM espaco 
        WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}