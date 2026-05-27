<?php

require_once(__DIR__ . "/../config/conexao.php");

class Usuario
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // =========================================================
    // LISTAR TODOS
    // =========================================================

    public function listarTodos()
    {

        $sql = "
            SELECT 
                u.id,
                u.nome,
                u.email,
                u.ativo,
                u.data_cadastro,
                f.nome_funcao
            FROM usuario u
            INNER JOIN funcao f
                ON u.id_funcao = f.id
            ORDER BY u.nome ASC
        ";

        return $this->conexao->query($sql);
    }

    // =========================================================
    // BUSCAR POR ID
    // =========================================================

    public function buscarPorId($id)
    {

        $sql = "
            SELECT 
                u.id,
                u.nome,
                u.email,
                u.id_funcao,
                u.ativo,
                u.data_cadastro,
                f.nome_funcao
            FROM usuario u
            INNER JOIN funcao f
                ON u.id_funcao = f.id
            WHERE u.id = ?
        ";

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
            INSERT INTO usuario (
                id_funcao,
                nome,
                email,
                senha_hash,
                ativo
            )
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bind_param(
            "isssi",
            $dados['id_funcao'],
            $dados['nome'],
            $dados['email'],
            $dados['senha_hash'],
            $dados['ativo']
        );

        return $stmt->execute();
    }

    // =========================================================
    // USUÁRIO EDITA A SI MESMO
    // =========================================================

    public function atualizarProprio($id, $dados)
    {

        $sql = "
            UPDATE usuario
            SET
                nome = ?,
                email = ?,
                senha_hash = ?
            WHERE id = ?
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bind_param(
            "sssi",
            $dados['nome'],
            $dados['email'],
            $dados['senha_hash'],
            $id
        );

        return $stmt->execute();
    }

    // =========================================================
    // ADMIN EDITA QUALQUER USUÁRIO
    // =========================================================

    public function atualizarAdmin($id, $dados)
    {

        $sql = "
            UPDATE usuario
            SET
                id_funcao = ?,
                nome = ?,
                email = ?,
                senha_hash = ?,
                ativo = ?
            WHERE id = ?
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bind_param(
            "isssii",
            $dados['id_funcao'],
            $dados['nome'],
            $dados['email'],
            $dados['senha_hash'],
            $dados['ativo'],
            $id
        );

        return $stmt->execute();
    }

    // =========================================================
    // EXCLUIR
    // =========================================================

    public function excluir($id)
    {

        $sql = "DELETE FROM usuario WHERE id = ?";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    // =========================================================
    // EMAIL EXISTE
    // =========================================================

    public function emailExiste($email, $ignorarId = null)
    {

        if ($ignorarId) {

            $sql = "
                SELECT id
                FROM usuario
                WHERE email = ?
                AND id != ?
            ";

            $stmt = $this->conexao->prepare($sql);

            $stmt->bind_param("si", $email, $ignorarId);

        } else {

            $sql = "
                SELECT id
                FROM usuario
                WHERE email = ?
            ";

            $stmt = $this->conexao->prepare($sql);

            $stmt->bind_param("s", $email);
        }

        $stmt->execute();

        return $stmt->get_result()->num_rows > 0;
    }
}

?>