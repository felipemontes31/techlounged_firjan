<?php

class Atividade
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
            INSERT INTO atividade (
                id_eixo, id_periodicidade, id_publico_alvo, nome_projeto, 
                objetivo, observacoes_gerais, eh_publico, url_imagem, 
                criado_por, atualizado_por
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param(
            "iiisssisii",
            $dados['id_eixo'],
            $dados['id_periodicidade'],
            $dados['id_publico_alvo'],
            $dados['nome_projeto'],
            $dados['objetivo'],
            $dados['observacoes_gerais'],
            $dados['eh_publico'],
            $dados['url_imagem'],
            $dados['criado_por'],
            $dados['atualizado_por']
        );
        return $stmt->execute();
    }

    // READ ALL (Com INNER JOIN para trazer os nomes em vez de apenas IDs na listagem)
    public function listarTodos()
    {
        $sql = "
            SELECT 
                a.*, 
                e.nome_eixo, 
                p.descricao AS nome_periodicidade, 
                pa.nome_publico,
                u_criador.nome AS nome_criador,
                u_editor.nome AS nome_editor
            FROM atividade a
            INNER JOIN eixo e ON a.id_eixo = e.id
            INNER JOIN periodicidade p ON a.id_periodicidade = p.id
            INNER JOIN publico_alvo pa ON a.id_publico_alvo = pa.id
            INNER JOIN usuario u_criador ON a.criado_por = u_criador.id
            INNER JOIN usuario u_editor ON a.atualizado_por = u_editor.id
            ORDER BY a.id DESC
        ";
        $resultado = $this->conexao->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // READ BY ID
    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM atividade WHERE id = ?";
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
            UPDATE atividade SET 
                id_eixo = ?, 
                id_periodicidade = ?, 
                id_publico_alvo = ?, 
                nome_projeto = ?, 
                objetivo = ?, 
                observacoes_gerais = ?, 
                eh_publico = ?, 
                url_imagem = ?, 
                atualizado_por = ?
            WHERE id = ?
        ";
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param(
            "iiisssisii",
            $dados['id_eixo'],
            $dados['id_periodicidade'],
            $dados['id_publico_alvo'],
            $dados['nome_projeto'],
            $dados['objetivo'],
            $dados['observacoes_gerais'],
            $dados['eh_publico'],
            $dados['url_imagem'],
            $dados['atualizado_por'],
            $dados['id']
        );
        return $stmt->execute();
    }

    // DELETE
    public function deletar($id)
    {
        $sql = "DELETE FROM atividade WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}