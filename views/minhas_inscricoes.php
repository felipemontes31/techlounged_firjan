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
</head>
<body>
<?php include(__DIR__ . '/../includes/topo.php'); ?>
<main class="tl-section">
  <div class="tl-container">
    <div class="tl-section-head">
      <div>
        <h1 style="color:var(--azul-institucional); margin:0;">Minhas inscrições</h1>
        <p>Acompanhe eventos confirmados, pendentes ou recusados. Eventos futuros ainda planejados podem ser cancelados.</p>
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
        const botaoDesinscrever = podeDesinscrever
          ? `<button class="tl-btn tl-btn-secondary" type="button" onclick="desinscrever(${parseInt(item.id_inscricao)})">Cancelar inscrição</button>`
          : `<button class="tl-btn tl-btn-disabled" type="button" disabled>Cancelamento indisponível</button>`;

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
              <div class="tl-event-footer">${botaoDesinscrever}</div>
            </div>
          </article>`;
      });
    })
    .catch(() => alvo.innerHTML = '<div class="tl-empty" style="grid-column:1/-1;">Não foi possível carregar suas inscrições.</div>');
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
</script>
</body>
</html>
