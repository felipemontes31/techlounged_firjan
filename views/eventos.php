<?php require_once(__DIR__ . '/../config/app.php'); if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eventos | TechLounged</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/techlounged.css">
</head>
<body>
<?php include(__DIR__ . '/../includes/topo.php'); ?>

<main>
  <section class="tl-hero">
    <div class="tl-container tl-hero-grid">
      <div>
        <span class="tl-kicker">Agenda da Biblioteca</span>
        <h1>Eventos, oficinas e experiências da biblioteca em um só lugar.</h1>
        <p>Consulte a programação pública, reserve sua participação e acompanhe suas inscrições no estilo de uma plataforma de ingressos.</p>
        <div class="tl-actions">
          <a class="tl-btn tl-btn-primary" href="#eventos">Ver eventos disponíveis</a>
          <?php if (!$usuarioLogado): ?>
            <a class="tl-btn tl-btn-secondary" href="<?= tl_url('views/login.php') ?>">Entrar para se inscrever</a>
          <?php else: ?>
            <a class="tl-btn tl-btn-secondary" href="<?= tl_url('views/minhas_inscricoes.php') ?>">Minhas inscrições</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="tl-hero-panel">
        <div class="tl-card tl-card-pad">
          <span class="tl-badge">Midiateca</span>
          <h2 style="color:var(--azul-institucional); margin:14px 0 8px;">Programação ativa</h2>
          <p style="color:var(--texto-suave); line-height:1.55; margin:0;">Oficinas, Cine Biblioteca, atividades formativas e encontros culturais com controle de vagas e inscrições.</p>
          <div class="tl-stat-grid">
            <div class="tl-stat"><strong id="statEventos">0</strong><span>eventos</span></div>
            <div class="tl-stat"><strong id="statVagas">0</strong><span>vagas previstas</span></div>
            <div class="tl-stat"><strong id="statAbertos">0</strong><span>abertos</span></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="tl-section" id="eventos">
    <div class="tl-container">
      <div class="tl-section-head">
        <div>
          <h2>Eventos em destaque</h2>
          <p>Filtre por período e status para encontrar a próxima atividade da biblioteca.</p>
        </div>
        <?php if (tl_usuario_eh_admin()): ?>
          <button class="tl-btn tl-btn-primary" type="button" onclick="abrirModalCadastro()">+ Criar evento</button>
        <?php endif; ?>
      </div>

      <div class="tl-card tl-filter">
        <div class="tl-field"><label>Data inicial</label><input type="date" id="f_data_execucao"></div>
        <div class="tl-field"><label>Data final</label><input type="date" id="f_data_finalizacao"></div>
        <div class="tl-field"><label>Status</label><select id="f_status"><option value="">Todos</option><option value="Planejado">Planejado</option><option value="Concluído">Concluído</option><option value="Cancelado">Cancelado</option></select></div>
        <div class="tl-field"><label>Busca visual</label><input type="search" id="f_busca_local" placeholder="Nome, tema ou espaço"></div>
        <button class="tl-btn tl-btn-primary" type="button" onclick="carregarDadosDoPainel()">Filtrar</button>
      </div>

      <div id="subTituloSessao" class="tl-meta" style="margin-bottom:14px;"></div>
      <div class="tl-event-grid" id="containerEventos"></div>
    </div>
  </section>
</main>

<div class="tl-modal-overlay" id="overlay" onclick="tlFecharModais()"></div>

<div class="tl-modal" id="modalRegistroAtividade">
  <h3 id="modalTitulo">Criar Registro de Atividade</h3>
  <form id="formRegistro" class="tl-form-grid">
    <input type="hidden" id="id" name="id">
    <div class="tl-field"><label>Atividade *</label><select id="id_atividade" name="id_atividade" required><option value="">Carregando...</option></select></div>
    <div class="tl-field"><label>Espaço *</label><select id="id_espaco" name="id_espaco" required><option value="">Carregando...</option></select></div>
    <div class="tl-field tl-full"><label>Tema específico</label><input type="text" id="tema_especifico" name="tema_especifico" placeholder="Ex: Oficina de Robótica Básica"></div>
    <div class="tl-field"><label>Data de execução *</label><input type="date" id="data_execucao" name="data_execucao" required></div>
    <div class="tl-field"><label>Data de finalização</label><input type="date" id="data_finalizacao" name="data_finalizacao"></div>
    <div class="tl-field"><label>Status</label><select id="status" name="status"><option value="Planejado">Planejado</option><option value="Concluído">Concluído</option><option value="Cancelado">Cancelado</option></select></div>
    <div class="tl-field"><label>Público previsto</label><input type="number" id="publico_previsto" name="publico_previsto" min="0"></div>
    <div class="tl-field"><label>Público realizado</label><input type="number" id="publico_realizado" name="publico_realizado" min="0" value="0"></div>
    <div class="tl-field"><label>URL da imagem</label><input type="url" id="url_imagem" name="url_imagem" placeholder="https://..."></div>
    <div class="tl-field tl-full"><label><input type="checkbox" id="confirm_auto" name="confirm_auto" value="1" checked> Confirmar inscrições automaticamente se houver vagas</label></div>
    <div class="tl-actions tl-full"><button class="tl-btn tl-btn-primary" type="submit" id="btnSalvarRegistro">Salvar</button><button class="tl-btn tl-btn-secondary" type="button" onclick="tlFecharModais()">Cancelar</button></div>
  </form>
</div>

<div class="tl-modal" id="modalInscritos">
  <h3>Gerenciamento de inscritos</h3>
  <div id="estatisticasEvento" class="tl-meta" style="margin-bottom:12px;"></div>
  <div class="tl-table-wrap"><table class="tl-table"><thead><tr><th>Nome/Matrícula</th><th>Tipo</th><th>Status</th></tr></thead><tbody id="listaInscritosCorpo"></tbody></table></div>
  <div class="tl-actions" style="margin-top:14px;"><button class="tl-btn tl-btn-secondary" onclick="tlFecharModais()">Fechar</button></div>
</div>

<script src="<?= tl_url('assets/js/techlounged.js') ?>"></script>
<script>
const controllerPainel = '<?= BASE_URL ?>/services/registro_atividade_controle.php';
const perfilUsuarioLogado = "<?= tl_usuario_eh_admin() ? 'Administrador' : (tl_usuario_eh_comum() ? 'Comum' : 'Visitante') ?>";
const eventoFiltradoId = new URLSearchParams(window.location.search).get('id_evento') || '';
let idRegistroInscritosAtual = null;
let capacidadeAtual = 0;

document.addEventListener('DOMContentLoaded', () => {
  carregarDadosDoPainel();
  if (perfilUsuarioLogado === 'Administrador') carregarSelectsAuxiliares();
  document.getElementById('f_busca_local').addEventListener('input', filtrarCardsLocalmente);
});

function obterParametroUrl(nome) {
    const parametros = new URLSearchParams(window.location.search);
    return parametros.get(nome) || "";
}

function obterQueryFiltros() {
    const idEvento = obterParametroUrl("id_evento");

    const dataInicial = document.getElementById("f_data_execucao")?.value || "";
    const dataFinal = document.getElementById("f_data_finalizacao")?.value || "";
    const status = document.getElementById("f_status")?.value || "";
    const busca = document.getElementById("f_busca")?.value || "";

    return (
        `&f_data_execucao=${encodeURIComponent(dataInicial)}` +
        `&f_data_finalizacao=${encodeURIComponent(dataFinal)}` +
        `&f_status=${encodeURIComponent(status)}` +
        `&f_busca=${encodeURIComponent(busca)}` +
        `&f_id_registro=${encodeURIComponent(idEvento)}`
    );
}

function rotaPorPerfil() {
  if (perfilUsuarioLogado === 'Administrador') return 'listar_admin';
  if (perfilUsuarioLogado === 'Comum') return 'listar_disponiveis_comum';
  return 'listar_publico';
}

function carregarDadosDoPainel() {
  document.getElementById('subTituloSessao').innerText = eventoFiltradoId ? `Visualização ativa: ${perfilUsuarioLogado} • Evento filtrado pelo QR Code` : `Visualização ativa: ${perfilUsuarioLogado}`;
  fetch(`${controllerPainel}?acao=${rotaPorPerfil()}${obterQueryFiltros()}`)
    .then(res => res.json())
    .then(res => renderizarEventos(res.sucesso ? res.dados : []))
    .catch(() => renderizarEventos([]));
}

function renderizarEventos(eventos) {
  const container = document.getElementById('containerEventos');
  container.innerHTML = '';
  document.getElementById('statEventos').innerText = eventos.length;
  document.getElementById('statVagas').innerText = eventos.reduce((acc, ev) => acc + (parseInt(ev.publico_previsto || ev.capacidade_maxima || 0)), 0);
  document.getElementById('statAbertos').innerText = eventos.filter(ev => ev.status === 'Planejado').length;

  if (!eventos.length) {
    container.innerHTML = '<div class="tl-empty" style="grid-column:1/-1;">Nenhum evento encontrado para os critérios selecionados.</div>';
    return;
  }

  eventos.forEach(evento => {
    const totalConfirmados = parseInt(evento.total_confirmados || 0);
    const totalPensando = parseInt(evento.total_pensando || 0);
    const img = evento.imagem_exibicao || evento.url_imagem || 'https://placehold.co/640x360/E2EBF4/004B87?text=TechLounged';
    const privado = evento.eh_publico == 1 ? '' : '<span class="tl-badge danger">Restrito</span>';
    const statusClasse = evento.status === 'Cancelado' ? 'danger' : (evento.status === 'Concluído' ? 'success' : '');

    let acoes = '';
    if (perfilUsuarioLogado === 'Administrador') {
      const botaoPanfleto = evento.eh_publico == 1
        ? `<a class="tl-btn tl-btn-secondary" target="_blank" href="<?= tl_url('views/admin/panfleto_evento.php') ?>?id=${parseInt(evento.id)}">Panfleto PDF</a>`
        : '';

      acoes = `
        <button class="tl-btn tl-btn-secondary" onclick="editarRegistro(${evento.id})">Editar</button>
        <button class="tl-btn tl-btn-primary" onclick="abrirGerenciadorInscritos(${evento.id}, ${parseInt(evento.capacidade_maxima || evento.publico_previsto || 0)})">Inscritos: ${totalConfirmados + totalPensando}</button>
        ${botaoPanfleto}`;
    } else if (perfilUsuarioLogado === 'Comum') {
      const idMinhaInscricao = evento.id_inscricao_usuario || evento.id_inscricao || null;
      const minhaSituacao = evento.minha_status_inscricao || 'Pendente';
      const meuTipo = evento.minha_tipo_inscricao || 'Pensando';
      const eventoEncerrado = evento.status === 'Cancelado' || evento.status === 'Concluído';

      if (idMinhaInscricao) {
        acoes = `
          <button class="tl-btn tl-btn-disabled" type="button" disabled>Já inscrito • ${tlTextoSeguro(minhaSituacao)} / ${tlTextoSeguro(meuTipo)}</button>
          <a class="tl-btn tl-btn-secondary" href="<?= tl_url('views/minhas_inscricoes.php') ?>">Ver minhas inscrições</a>`;
      } else if (eventoEncerrado) {
        acoes = `<button class="tl-btn tl-btn-disabled" type="button" disabled>Inscrição indisponível</button>`;
      } else {
        acoes = `
          <button class="tl-btn tl-btn-primary" onclick="inscrever(${evento.id}, 'Confirmado')">Confirmar inscrição</button>
          <button class="tl-btn tl-btn-secondary" onclick="inscrever(${evento.id}, 'Pensando')">Tenho interesse</button>`;
      }
    } else {
      acoes = `<a class="tl-btn tl-btn-primary" href="<?= tl_url('views/login.php') ?>">Entrar para se inscrever</a>`;
    }

    container.innerHTML += `
      <article class="tl-card tl-event-card" data-busca="${tlTextoSeguro(`${evento.nome_projeto} ${evento.tema_especifico || ''} ${evento.nome_espaco || ''}`).toLowerCase()}">
        <img class="tl-event-img" src="${tlTextoSeguro(img)}" alt="Imagem do evento ${tlTextoSeguro(evento.nome_projeto)}">
        <div class="tl-event-body">
          <div class="tl-badges"><span class="tl-badge ${statusClasse}">${tlTextoSeguro(evento.status || 'Planejado')}</span>${privado}</div>
          <h3 class="tl-event-title">${tlTextoSeguro(evento.nome_projeto)}</h3>
          <div class="tl-meta">📅 ${tlDataBR(evento.data_execucao)} ${evento.data_finalizacao ? 'até ' + tlDataBR(evento.data_finalizacao) : ''}</div>
          <div class="tl-meta">📍 ${tlTextoSeguro(evento.nome_espaco || 'Espaço a definir')} • Vagas: ${tlTextoSeguro(evento.publico_previsto || evento.capacidade_maxima || 'A definir')}</div>
          <p class="tl-meta"><strong>Tema:</strong> ${tlTextoSeguro(evento.tema_especifico || 'Geral')}</p>
          <div class="tl-event-footer">${acoes}</div>
        </div>
      </article>`;
  });
  filtrarCardsLocalmente();
}

function filtrarCardsLocalmente() {
  const termo = document.getElementById('f_busca_local').value.toLowerCase().trim();
  document.querySelectorAll('[data-busca]').forEach(card => {
    card.style.display = card.dataset.busca.includes(termo) ? '' : 'none';
  });
}

function carregarSelectsAuxiliares() {
  fetch(`${controllerPainel}?acao=carregar_selects_cadastro`)
    .then(res => res.json())
    .then(res => {
      if (!res.sucesso) return;
      const selectAtiv = document.getElementById('id_atividade');
      const selectEsp = document.getElementById('id_espaco');
      selectAtiv.innerHTML = '<option value="">Selecione uma atividade matriz...</option>';
      selectEsp.innerHTML = '<option value="">Selecione o espaço...</option>';
      res.dados.atividades.forEach(ativ => selectAtiv.innerHTML += `<option value="${ativ.id}">${tlTextoSeguro(ativ.nome_projeto)}</option>`);
      res.dados.espacos.forEach(esp => selectEsp.innerHTML += `<option value="${esp.id}">${tlTextoSeguro(esp.nome_espaco)} (Máx: ${esp.capacidade_maxima})</option>`);
    });
}

function abrirModalCadastro() {
  document.getElementById('formRegistro').reset();
  document.getElementById('id').value = '';
  document.getElementById('modalTitulo').innerText = 'Criar Registro de Atividade';
  tlAbrirModal('modalRegistroAtividade');
}

document.getElementById('formRegistro').addEventListener('submit', function(e) {
  e.preventDefault();
  const id = document.getElementById('id').value;
  const acao = id ? 'atualizar' : 'criar';
  fetch(`${controllerPainel}?acao=${acao}`, { method: 'POST', body: new FormData(this) })
    .then(res => res.json())
    .then(res => { alert(res.mensagem); if (res.sucesso) { tlFecharModais(); carregarDadosDoPainel(); } });
});

function editarRegistro(idRegistro) {
  fetch(`${controllerPainel}?acao=buscar&id=${idRegistro}`)
    .then(res => res.json())
    .then(res => {
      if (!res.sucesso) return alert('Erro ao buscar dados do registro.');
      document.getElementById('modalTitulo').innerText = 'Editar Registro de Atividade';
      ['id','id_atividade','id_espaco','tema_especifico','data_execucao','data_finalizacao','status','publico_previsto','publico_realizado','url_imagem'].forEach(campo => {
        const el = document.getElementById(campo);
        if (el) el.value = res.dados[campo] || '';
      });
      document.getElementById('confirm_auto').checked = res.dados.confirm_auto == 1;
      tlAbrirModal('modalRegistroAtividade');
    });
}

function inscrever(idRegistro, tipo) {
  const formData = new FormData();
  formData.append('id_registro_atividade', idRegistro);
  formData.append('tipo_inscricao', tipo);

  fetch(`${controllerPainel}?acao=inscrever`, { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
      if (res.mensagem === 'login_obrigatorio') {
        window.location.href = '<?= tl_url('views/login.php') ?>';
        return;
      }

      alert(res.mensagem);
      carregarDadosDoPainel();
    });
}

function abrirGerenciadorInscritos(idRegistro, capacidade) {
  idRegistroInscritosAtual = idRegistro;
  capacidadeAtual = capacidade;
  document.getElementById('estatisticasEvento').innerText = `Capacidade máxima: ${capacidade} vagas`;
  tlAbrirModal('modalInscritos');
  fetch(`${controllerPainel}?acao=listar_inscritos_evento&id_registro=${idRegistro}`)
    .then(res => res.json())
    .then(res => {
      const corpo = document.getElementById('listaInscritosCorpo');
      corpo.innerHTML = '';
      if (!res.sucesso || !res.dados.length) {
        corpo.innerHTML = '<tr><td colspan="3">Ninguém inscrito ainda.</td></tr>';
        return;
      }
      res.dados.forEach(insc => {
        corpo.innerHTML += `<tr><td>${tlTextoSeguro(insc.nome)} <small>${tlTextoSeguro(insc.matricula || 'Sem matrícula')}</small></td><td>${tlTextoSeguro(insc.tipo_inscricao)}</td><td><select onchange="mudarStatusInscricaoAdmin(${parseInt(insc.id)}, this.value)"><option value="Pendente" ${insc.status_inscricao === 'Pendente' ? 'selected' : ''}>Pendente</option><option value="Confirmado" ${insc.status_inscricao === 'Confirmado' ? 'selected' : ''}>Confirmado</option><option value="Recusada" ${insc.status_inscricao === 'Recusada' ? 'selected' : ''}>Recusada</option></select></td></tr>`;
      });
    });
}

function mudarStatusInscricaoAdmin(idInscricao, novoStatus) {
  const formData = new FormData();
  formData.append('id_inscricao', idInscricao);
  formData.append('status_inscricao', novoStatus);
  fetch(`${controllerPainel}?acao=modificar_status_admin`, { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => { alert(res.mensagem); carregarDadosDoPainel(); if (idRegistroInscritosAtual) abrirGerenciadorInscritos(idRegistroInscritosAtual, capacidadeAtual); });
}
</script>
</body>
</html>
