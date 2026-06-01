<?php
require_once(__DIR__ . '/../middleware/auth.php');
require_once(__DIR__ . '/../config/app.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Minhas inscrições | TechLounged</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/techlounged.css">
  <style>
    .tl-actions-wrap {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }
    .tl-btn-outline-danger {
      background: transparent;
      color: #b42318;
      border-color: rgba(180, 35, 24, 0.28);
    }
    .tl-btn-outline-danger:hover {
      background: rgba(180, 35, 24, 0.08);
    }
  </style>
</head>
<body>
<?php include(__DIR__ . '/../includes/topo.php'); ?>
<main class="tl-section">
  <div class="tl-container">
    <div class="tl-section-head">
      <div>
        <h1 style="color:var(--azul-institucional); margin:0;">Minhas inscrições</h1>
        <p>Acompanhe eventos confirmados, pendentes ou recusados. Eventos futuros ainda planejados podem ser alterados ou cancelados.</p>
      </div>
      <a class="tl-btn tl-btn-primary" href="<?= tl_url('views/eventos.php') ?>">Encontrar novos eventos</a>
    </div>
    <div class="tl-event-grid" id="listaMinhasInscricoes"></div>
  </div>
</main>
<script src="<?= tl_url('assets/js/techlounged.js') ?>"></script>
<script>
const controllerPainel = '<?= BASE_URL ?>/services/registro_atividade_controle.php';
document.addEventListener('DOMContentLoaded', carregarMinhasInscricoes);

function carregarMinhasInscricoes() {
  const alvo = document.getElementById('listaMinhasInscricoes');
  alvo.innerHTML = '<div class="tl-empty" style="grid-column:1/-1;">Carregando inscrições...</div>';

  fetch(`${controllerPainel}?acao=minhas_inscricoes`)
    .then(res => res.json())
    .then(res => {
      alvo.innerHTML = '';

      if (!res.sucesso || !res.dados.length) {
        alvo.innerHTML = '<div class="tl-empty" style="grid-column:1/-1;">Você ainda não possui inscrições. Acesse a agenda e escolha um evento.</div>';
        return;
      }

      res.dados.forEach(item => {
        const classe = item.status_inscricao === 'Confirmado' ? 'success' : (item.status_inscricao === 'Recusada' ? 'danger' : 'warn');
        const podeDesinscrever = parseInt(item.pode_desinscrever || 0) === 1;
        const idInscricao = parseInt(item.id_inscricao);
        const tipoAtual = item.tipo_inscricao || 'Pensando';
        const statusEvento = item.status_evento || 'Planejado';
        const podeAlterar = podeDesinscrever && statusEvento === 'Planejado';

        const botaoConfirmar = podeAlterar && tipoAtual !== 'Confirmado'
          ? `<button class="tl-btn tl-btn-primary" type="button" onclick="alterarTipoInscricao(${idInscricao}, 'Confirmado')">Confirmar presença</button>`
          : '';

        const botaoInteresse = podeAlterar && tipoAtual !== 'Pensando'
          ? `<button class="tl-btn tl-btn-secondary" type="button" onclick="alterarTipoInscricao(${idInscricao}, 'Pensando')">Tenho interesse</button>`
          : '';

        const botaoDesinscrever = podeDesinscrever
          ? `<button class="tl-btn tl-btn-outline-danger" type="button" onclick="desinscrever(${idInscricao})">Cancelar inscrição</button>`
          : `<button class="tl-btn tl-btn-disabled" type="button" disabled>Cancelamento indisponível</button>`;

        // Declarar e processar a lógica do botão da agenda ANTES do wrap de botões
        let botaoAgenda = '';

        if (item.status_inscricao === 'Confirmado') {
            // Passar o "item" completo para a função ter acesso aos dados (nome_projeto, data, etc.)
            const urlGoogleAgenda = gerarLinkGoogleAgenda(item);
            
            botaoAgenda = `
                <a href="${urlGoogleAgenda}" target="_blank" rel="noopener noreferrer" 
                  class="tl-btn tl-btn-primary">
                  📅 Marcar na Agenda
                </a>
            `;
        }

        const botoesAcao = `
          <div class="tl-actions-wrap">
            ${botaoConfirmar}
            ${botaoInteresse}
            ${botaoDesinscrever}
            ${botaoAgenda}
          </div>
        `;

        alvo.innerHTML += `
          <article class="tl-card tl-event-card">
            <img class="tl-event-img" src="${tlTextoSeguro(item.imagem_exibicao || item.url_imagem || 'https://placehold.co/640x360/E2EBF4/004B87?text=Minha+Inscrição')}" alt="Evento">
            <div class="tl-event-body">
              <div class="tl-badges">
                <span class="tl-badge ${classe}">${tlTextoSeguro(item.status_inscricao || 'Pendente')}</span>
                <span class="tl-badge">${tlTextoSeguro(item.tipo_inscricao || 'Pensando')}</span>
                <span class="tl-badge ${item.status_evento === 'Cancelado' ? 'danger' : ''}">${tlTextoSeguro(item.status_evento || 'Planejado')}</span>
              </div>
              <h3 class="tl-event-title">${tlTextoSeguro(item.nome_projeto)}</h3>
              <div class="tl-meta">📅 ${tlDataBR(item.data_execucao)} ${item.data_finalizacao ? 'até ' + tlDataBR(item.data_finalizacao) : ''}</div>
              <div class="tl-meta">📍 ${tlTextoSeguro(item.nome_espaco || 'Espaço a definir')}</div>
              <p class="tl-meta"><strong>Tema:</strong> ${tlTextoSeguro(item.tema_especifico || 'Geral')}</p>
              <div class="tl-event-footer">${botoesAcao}</div>
            </div>
          </article>`;
      });
    })
    .catch(() => alvo.innerHTML = '<div class="tl-empty" style="grid-column:1/-1;">Não foi possível carregar suas inscrições.</div>');
}


function alterarTipoInscricao(idInscricao, novoTipo) {
  const mensagem = novoTipo === 'Confirmado'
    ? 'Deseja confirmar sua presença neste evento?'
    : 'Deseja alterar sua inscrição para tenho interesse?';

  if (!confirm(mensagem)) return;

  const formData = new FormData();
  formData.append('id_inscricao', idInscricao);
  formData.append('tipo_inscricao', novoTipo);

  fetch(`${controllerPainel}?acao=alterar_inscricao_comum`, {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(res => {
      alert(res.mensagem);
      if (res.sucesso) carregarMinhasInscricoes();
    })
    .catch(() => alert('Erro de comunicação ao alterar inscrição.'));
}

function desinscrever(idInscricao) {
  if (!confirm('Deseja cancelar sua inscrição neste evento?')) return;

  const formData = new FormData();
  formData.append('id_inscricao', idInscricao);

  fetch(`${controllerPainel}?acao=desinscrever`, {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(res => {
      alert(res.mensagem);
      if (res.sucesso) carregarMinhasInscricoes();
    })
    .catch(() => alert('Erro de comunicação ao cancelar inscrição.'));
}

function gerarLinkGoogleAgenda(evento) {
    const baseUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE";
    
    // 1. Define o título do evento
    const titulo = encodeURIComponent(`[TechLounge] ${evento.nome_projeto} - ${evento.tema_especifico || ''}`);
    
    // 2. Define a descrição/detalhes
    const detalhes = encodeURIComponent(`Sua inscrição está confirmada!\nLocal: ${evento.nome_espaco}\nStatus: ${evento.status_inscricao}`);
    
    // 3. Define o local (Espaço Físico)
    const local = encodeURIComponent(evento.nome_espaco);
    
    // 4. Formatação de data para o padrão do Google (YYYYMMDDTHHMMSS)
    // Se o seu banco guarda apenas a data (Ex: 2026-06-15), definimos um horário padrão (ex: das 14:00 às 16:00)
    // Se guardar data e hora juntas, basta extrair e limpar os traços e dois-pontos.
    
    const dataLimpa = evento.data_execucao.replace(/-/g, ''); // Transforma 2026-06-15 em 20260615
    const dataInicio = `${dataLimpa}T140000`; // Exemplo: Início às 14:00
    const dataFim = `${dataLimpa}T160000`;    // Exemplo: Término às 16:00
    
    const datas = `${dataInicio}/${dataFim}`;

    // Monta a URL final
    return `${baseUrl}&text=${titulo}&dates=${datas}&details=${detalhes}&location=${local}`;
}
</script>
</body>
</html>
