<?php

class Inscricao
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // Regra Inteligente de Inscrição
    public function realizarInscricao($id_registro, $id_usuario, $tipo_inscricao)
    {
        // 1. Buscar os dados do evento e o perfil do usuário de uma vez só
        $sqlEvento = "
            SELECT ra.confirm_auto, ra.data_execucao, a.eh_publico, e.capacidade_maxima, u.matricula
            FROM registro_atividade ra
            INNER JOIN atividade a ON ra.id_atividade = a.id
            INNER JOIN espaco e ON ra.id_espaco = e.id
            CROSS JOIN usuario u
            WHERE ra.id = ? AND u.id = ?
        ";
        $stmt = $this->conexao->prepare($sqlEvento);
        $stmt->bind_param("ii", $id_registro, $id_usuario);
        $stmt->execute();
        $evento = $stmt->get_result()->fetch_assoc();

        if (!$evento) {
            return ['sucesso' => false, 'mensagem' => 'Evento ou usuário inválido.'];
        }

        // Bloqueio de segurança temporal: Não se inscrever em eventos que já passaram
        if (strtotime($evento['data_execucao']) < strtotime(date('Y-m-d'))) {
            return ['sucesso' => false, 'mensagem' => 'Não é possível se inscrever em eventos passados.'];
        }

        // [NOVA REGRA]: Bloqueia se o evento for privado (eh_publico=0) e o usuário não tiver matrícula
        if ($evento['eh_publico'] == 0 && (empty($evento['matricula']) || $evento['matricula'] == null)) {
            return ['sucesso' => false, 'mensagem' => 'Este evento é restrito para ambiente interno. Você precisa ter uma matrícula ativa vinculada para poder se inscrever.'];
        }

        // 2. Contabilizar ocupação atual confirmada
        $sqlContagem = "SELECT COUNT(*) as total FROM inscricao WHERE id_registro_atividade = ? AND tipo_inscricao = 'Confirmado' AND status_inscricao = 'Confirmado'";
        $stmtC = $this->conexao->prepare($sqlContagem);
        $stmtC->bind_param("i", $id_registro);
        $stmtC->execute();
        $resContagem = $stmtC->get_result()->fetch_assoc();
        $totalInscritosConfirmados = $resContagem['total'];

        // 3. Determinar os status iniciais baseados nas regras informadas
        $status_inscricao = 'Pendente'; 
        $msgRetorno = "Inscrição realizada! Aguarde a confirmação de um administrador.";

        if ($tipo_inscricao === 'Confirmado') {
            // Caso 1: Evento Lotado
            if ($totalInscritosConfirmados >= $evento['capacidade_maxima']) {
                $status_inscricao = 'Pendente';
                $msgRetorno = "O evento está lotado! Sua inscrição foi colocada em lista de espera (Pendente). Por favor consulte a administração.";
            } 
            // Caso 2: Possui vagas e a confirmação automática está ativa
            elseif ($evento['confirm_auto'] == 1) {
                $status_inscricao = 'Confirmado';
                $msgRetorno = "Inscrição realizada e Confirmada automaticamente com sucesso!";
            }
        } else {
            // Se o tipo for 'Pensando', entra sempre como pendente
            $status_inscricao = 'Pendente';
            $msgRetorno = "Inscrição salva como 'Pensando'. Lembre-se de confirmar antes do evento.";
        }

        // 4. Inserir no banco de dados
        $sqlInsert = "INSERT INTO inscricao (id_registro_atividade, id_usuario_inscrito, tipo_inscricao, status_inscricao) VALUES (?, ?, ?, ?)";
        $stmtI = $this->conexao->prepare($sqlInsert);
        $stmtI->bind_param("iiss", $id_registro, $id_usuario, $tipo_inscricao, $status_inscricao);
        
        if ($stmtI->execute()) {
            return ['sucesso' => true, 'mensagem' => $msgRetorno];
        }
        return ['sucesso' => false, 'mensagem' => 'Erro técnico ao processar a inscrição.'];
    }

    // Usuário comum alterando sua própria inscrição (Muda status para Pendente automático)
    public function alterarMinhaInscricao($id_inscricao, $id_usuario, $novo_tipo)
    {
        $status_inscricao = 'Pendente';
        $sql = "UPDATE inscricao SET tipo_inscricao = ?, status_inscricao = ? WHERE id = ? AND id_usuario_inscrito = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ssii", $novo_tipo, $status_inscricao, $id_inscricao, $id_usuario);
        return $stmt->execute();
    }

    // Listar inscrições de um evento específico para o gerenciamento do Admin
    public function listarPorEvento($id_registro)
    {
        $sql = "SELECT i.*, u.nome, u.sobrenome, u.email, u.matricula FROM inscricao i 
                INNER JOIN usuario u ON i.id_usuario_inscrito = u.id 
                WHERE i.id_registro_atividade = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id_registro);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Administrador alterando status manualmente
    public function atualizarStatusAdmin($id_inscricao, $novo_status)
    {
        $sql = "UPDATE inscricao SET status_inscricao = ? WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("si", $novo_status, $id_inscricao);
        return $stmt->execute();
    }
}