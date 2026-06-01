<?php

class Curso
{
    private mysqli $conexao;

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
    }

    public function listarTodos(): array
    {
        $sql = "SELECT id, nome_curso, descricao FROM cursos ORDER BY nome_curso ASC";
        $resultado = $this->conexao->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT id, nome_curso, descricao FROM cursos WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $curso = $stmt->get_result()->fetch_assoc();
        return $curso ?: null;
    }

    public function nomeExiste(string $nomeCurso, int $idIgnorar = 0): bool
    {
        $sql = "SELECT id FROM cursos WHERE nome_curso = ?";
        $tipos = "s";
        $valores = [$nomeCurso];

        if ($idIgnorar > 0) {
            $sql .= " AND id <> ?";
            $tipos .= "i";
            $valores[] = $idIgnorar;
        }

        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param($tipos, ...$valores);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function criar(string $nomeCurso, ?string $descricao): bool
    {
        $sql = "INSERT INTO cursos (nome_curso, descricao) VALUES (?, ?)";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ss", $nomeCurso, $descricao);
        return $stmt->execute();
    }

    public function atualizar(int $id, string $nomeCurso, ?string $descricao): bool
    {
        $sql = "UPDATE cursos SET nome_curso = ?, descricao = ? WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ssi", $nomeCurso, $descricao, $id);
        return $stmt->execute();
    }

    public function deletar(int $id): bool
    {
        $sql = "DELETE FROM cursos WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
