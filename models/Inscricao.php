<?php

class Inscricao
{
    private $conexao;

    public function __construct($conexao)
    {
        $this->conexao = $conexao;
    }

    // Regra Inteligente de Inscrição com Prevenção de Duplicidade (Upsert)
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

        // Bloqueia se o evento for privado (eh_publico=0) e o usuário não tiver matrícula
        if ($evento['eh_publico'] == 0 && (empty($evento['matricula']) || $evento['matricula'] == null)) {
            return ['sucesso' => false, 'mensagem' => 'Este evento é restrito para ambiente interno. Você precisa ter uma matrícula ativa vinculada para poder se inscrever.'];
        }

        // 2. VERIFICAÇÃO DE DUPLICIDADE: O usuário já está inscrito neste evento?
        $sqlCheck = "SELECT id, tipo_inscricao, status_inscricao FROM inscricao WHERE id_registro_atividade = ? AND id_usuario_inscrito = ?";
        $stmtCheck = $this->conexao->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $id_registro, $id_usuario);
        $stmtCheck->execute();
        $inscricaoExistente = $stmtCheck->get_result()->fetch_assoc();

        // 3. Contabilizar ocupação atual confirmada (excluindo a inscrição do próprio usuário se ele já for um confirmado, para não contar ele mesmo duas vezes no cálculo de vagas)
        $sqlContagem = "
            SELECT COUNT(*) as total 
            FROM inscricao 
            WHERE id_registro_atividade = ? 
              AND tipo_inscricao = 'Confirmado' 
              AND status_inscricao = 'Confirmado'
        ";
        if ($inscricaoExistente) {
            $sqlContagem .= " AND id != " . intval($inscricaoExistente['id']);
        }

        $stmtC = $this->conexao->prepare($sqlContagem);
        $stmtC->bind_param("i", $id_registro);
        $stmtC->execute();
        $resContagem = $stmtC->get_result()->fetch_assoc();
        $totalInscritosConfirmados = $resContagem['total'];

        // 4. Determinar os status iniciais baseados nas regras de negócio
        $status_inscricao = 'Pendente'; 
        $msgRetorno = "";

        if ($tipo_inscricao === 'Confirmado') {
            // Caso A: Evento Lotado
            if ($totalInscritosConfirmados >= $evento['capacidade_maxima']) {
                $status_inscricao = 'Pendente';
                $msgRetorno = "O evento está lotado! Sua inscrição foi colocada em lista de espera (Pendente). Por favor consulte a administração.";
            } 
            // Caso B: Possui vagas e a confirmação automática está ativa
            elseif ($evento['confirm_auto'] == 1) {
                $status_inscricao = 'Confirmado';
                $msgRetorno = "Inscrição realizada e Confirmada automaticamente com sucesso!";
            } else {
                $msgRetorno = "Inscrição realizada! Aguarde a confirmação de um administrador.";
            }
        } else {
            // Se o tipo for 'Pensando', entra sempre como pendente
            $status_inscricao = 'Pendente';
            $msgRetorno = "Inscrição salva como 'Pensando'. Lembre-se de confirmar antes do evento.";
        }

        // 5. EXECUÇÃO DO UPSERT (UPDATE OU INSERT)
        if ($inscricaoExistente) {
            // Se o usuário clicou no mesmo botão que já representava o estado dele, não faz nada
            if ($inscricaoExistente['tipo_inscricao'] === $tipo_inscricao) {
                return ['sucesso' => true, 'mensagem' => "Você já está registrado neste evento como '{$tipo_inscricao}' com status '{$inscricaoExistente['status_inscricao']}'."];
            }

            // Caso mude o tipo_inscrição, o status_inscricao deve mudar automaticamente para pendente (Regra solicitada)
            $sqlUpdate = "UPDATE inscricao SET tipo_inscricao = ?, status_inscricao = ? WHERE id = ?";
            $stmtU = $this->conexao->prepare($sqlUpdate);
            $stmtU->bind_param("ssi", $tipo_inscricao, $status_inscricao, $inscricaoExistente['id']);
            
            if ($stmtU->execute()) {
                return ['sucesso' => true, 'mensagem' => "Sua inscrição existente foi atualizada para '{$tipo_inscricao}'! " . $msgRetorno];
            }
        } else {
            // Caso não exista nenhuma inscrição prévia, faz o INSERT clássico
            $sqlInsert = "INSERT INTO inscricao (id_registro_atividade, id_usuario_inscrito, tipo_inscricao, status_inscricao) VALUES (?, ?, ?, ?)";
            $stmtI = $this->conexao->prepare($sqlInsert);
            $stmtI->bind_param("iiss", $id_registro, $id_usuario, $tipo_inscricao, $status_inscricao);
            
            if ($stmtI->execute()) {
                return ['sucesso' => true, 'mensagem' => $msgRetorno];
            }
        }

        return ['sucesso' => false, 'mensagem' => 'Erro técnico ao processar a inscrição.'];
    }

    // ... (o restante dos métodos como listarPorEvento e atualizarStatusAdmin continuam iguais)
}