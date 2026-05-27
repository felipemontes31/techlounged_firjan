<?php
// Garante o carregamento das validações de sessão
//require_once(__DIR__ . "/../../middleware/auth.php");//
require_once(__DIR__ . "/../../config/conexao.php");

// Opcional: Buscar os registros de atividades disponíveis do "Cine Biblioteca" para listar no Select
$id_usuario = $_SESSION['usuario']['id'];
$sqlAtividadesCine = "
    SELECT ra.id, a.nome_projeto, ra.data_execucao, ra.tema_especifico 
    FROM registro_atividade ra
    INNER JOIN atividade a ON a.id = ra.id_atividade
    WHERE a.nome_projeto = 'Cine Biblioteca'
    ORDER BY ra.data_execucao DESC
";
$resultadoCine = $conexao->query($sqlAtividadesCine);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cine Biblioteca - Novo Registro</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <header>
        <h1>Painel Cine Biblioteca</h1>
        <span style="font-size: 14px;">Olá, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?></span>
    </header>

    <main class="container">
        <div class="form-card">
            <h2>Vincular Novo Curta-Metragem</h2>
            
            <div id="apiFeedback" class="alert-box"></div>

            <form id="cineForm">
                
                <div class="form-group">
                    <label for="id_registro_atividade">Registro da Atividade Relacionada *</label>
                    <select id="id_registro_atividade" name="id_registro_atividade" required>
                        <option value="">Selecione a sessão do Cine Biblioteca...</option>
                        <?php while($reg = $resultadoCine->fetch_assoc()): ?>
                            <option value="<?= $reg['id'] ?>">
                                ID: <?= $reg['id'] ?> - <?= htmlspecialchars($reg['tema_specifico'] ?? 'Sessão Sem Tema') ?> (<?= date('d/m/Y', strtotime($reg['data_execucao'])) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="titulo_curta">Título do Curta *</label>
                    <input type="text" id="titulo_curta" name="titulo_curta" placeholder="Ex: O Xadrez das Cores" required>
                </div>

                <div class="form-group">
                    <label for="link">Link do Vídeo (URL)</label>
                    <input type="text" id="link" name="link" placeholder="Ex: https://youtube.com/watch?v=...">
                </div>

                <div class="form-group">
                    <label for="detalhes_controle">Detalhes / Controle Interno</label>
                    <textarea id="detalhes_controle" name="detalhes_controle" placeholder="Notas sobre a exibição, autorização ou observações técnicas..."></textarea>
                </div>

                <div class="actions-row">
                    <a href="index.php" class="btn-back">&larr; Voltar</a>
                    <button type="submit" id="btnSalvar">Salvar no Acervo</button>
                </div>

            </form>
        </div>
    </main>

    <script>
        document.getElementById('cineForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Impede a página de recarregar
            
            const form = e.target;
            const btnSalvar = document.getElementById('btnSalvar');
            const feedback = document.getElementById('apiFeedback');
            
            // Desabilita o botão para evitar cliques duplos involuntários
            btnSalvar.disabled = true;
            btnSalvar.textContent = "Salvando...";

            // Captura os dados digitados
            const formData = new FormData(form);

            /* IMPORTANTE: Mude o caminho abaixo para bater EXATAMENTE 
              com o local onde está salvo o SEU arquivo PHP de tratamento de dados.
            */
            fetch('../services/cine_biblioteca/criar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na comunicação com o servidor.');
                }
                return response.json(); // Interpreta o respostaJSON do PHP
            })
            .then(data => {
                feedback.style.display = 'block';
                
                if (data.sucesso || data.status === true) { // Trata o retorno da sua função respostaJSON
                    feedback.className = 'alert-box alert-success';
                    feedback.textContent = data.mensagem || "Registro salvo com sucesso!";
                    form.reset(); // Limpa os campos preenchidos
                } else {
                    feedback.className = 'alert-box alert-danger';
                    feedback.textContent = data.mensagem || "Erro ao processar requisição.";
                }
            })
            .catch(error => {
                feedback.style.display = 'block';
                feedback.className = 'alert-box alert-danger';
                feedback.textContent = "Erro de rede: Não foi possível conectar ao servidor.";
                console.error(error);
            })
            .finally(() => {
                // Reativa o botão após o processamento terminar
                btnSalvar.disabled = false;
                btnSalvar.textContent = "Salvar no Acervo";
            });
        });
    </script>
</body>
</html>