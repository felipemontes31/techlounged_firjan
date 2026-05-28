<?php
require_once(__DIR__ . '/../../middleware/permissao.php');
verificarPermissao(['Administrador', 'Bibliotecário', 'Bibliotecario']);
require_once(__DIR__ . '/../../config/app.php');

$idRegistro = intval($_GET['id'] ?? 0);
if ($idRegistro <= 0) {
    echo "ID do registro de evento não informado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Relatório de inscritos | TechLounged</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/techlounged.css">
  <style>
    .tl-report-page { max-width: 1120px; margin: 0 auto; padding: 28px 18px 48px; }
    .tl-report-toolbar { display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:20px; }
    .tl-report-title { margin:0; color:var(--azul-institucional); }
    .tl-report-subtitle { margin:6px 0 0; color:var(--texto-suave); }
    .tl-report-grid { display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap:14px; margin:18px 0; }
    .tl-report-stat { padding:18px; border:1px solid var(--borda); border-radius:18px; background:rgba(255,255,255,.92); box-shadow:var(--sombra); }
    .tl-report-stat strong { display:block; font-size:30px; color:var(--azul-institucional); line-height:1; }
    .tl-report-stat span { display:block; margin-top:8px; color:var(--texto-suave); font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
    .tl-chart-grid { display:grid; grid-template-columns: 1fr 1fr; gap:18px; margin:20px 0; }
    .tl-chart-card { padding:20px; border:1px solid var(--borda); border-radius:20px; background:rgba(255,255,255,.92); box-shadow:var(--sombra); }
    .tl-chart-card h2 { margin:0 0 6px; color:var(--azul-institucional); font-size:20px; }
    .tl-chart-card p { margin:0 0 18px; color:var(--texto-suave); font-size:13px; }
    .tl-bar-row { margin:14px 0; }
    .tl-bar-label { display:flex; justify-content:space-between; gap:12px; font-weight:800; color:var(--texto); margin-bottom:8px; }
    .tl-bar-track { height:34px; border-radius:999px; background:var(--azul-suave); overflow:hidden; border:1px solid var(--borda); }
    .tl-bar-fill { height:100%; border-radius:999px; display:flex; align-items:center; justify-content:flex-end; padding-right:12px; color:white; font-size:12px; font-weight:900; min-width:38px; transition:.25s ease; background:var(--azul-dinamico); }
    .tl-bar-fill.is-soft { background:var(--azul-institucional); }
    .tl-print-only { display:none; }
    .tl-table small { color:var(--texto-suave); }
    @media (max-width: 900px) {
      .tl-report-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .tl-chart-grid { grid-template-columns: 1fr; }
      .tl-report-toolbar { align-items:flex-start; flex-direction:column; }
    }
    @media print {
      body { background:white !important; color:#111 !important; }
      .tl-site-header, .tl-report-actions, .tl-theme-toggle { display:none !important; }
      .tl-report-page { max-width:none; padding:0; }
      .tl-card, .tl-chart-card, .tl-report-stat { box-shadow:none !important; break-inside:avoid; }
      .tl-print-only { display:block; }
      .tl-table-wrap { overflow:visible; }
      .tl-table { font-size:11px; }
      .tl-report-grid { grid-template-columns: repeat(4, 1fr); }
      .tl-chart-grid { grid-template-columns: 1fr 1fr; }
    }
  </style>
</head>
<body>
<?php include(__DIR__ . '/../../includes/topo.php'); ?>

<main class="tl-report-page">
  <div class="tl-report-toolbar">
    <div>
      <span class="tl-pill">Relatório administrativo</span>
      <h1 class="tl-report-title" id="tituloEvento">Carregando relatório...</h1>
      <p class="tl-report-subtitle" id="descricaoEvento">Aguarde enquanto buscamos os dados do evento e dos inscritos.</p>
    </div>
    <div class="tl-actions tl-report-actions">
      <a class="tl-btn tl-btn-secondary" href="<?= tl_url('views/admin/registros.php') ?>">Voltar</a>
      <button class="tl-btn tl-btn-primary" type="button" onclick="window.print()">Imprimir / Salvar PDF</button>
    </div>
  </div>

  <section class="tl-card tl-card-pad" style="margin-bottom:18px;">
    <div id="dadosEvento" class="tl-meta">Carregando informações do evento...</div>
  </section>

  <section class="tl-report-grid">
    <div class="tl-report-stat"><strong id="statVagas">0</strong><span>Vagas disponíveis</span></div>
    <div class="tl-report-stat"><strong id="statConfirmados">0</strong><span>Inscritos confirmados</span></div>
    <div class="tl-report-stat"><strong id="statInteresse">0</strong><span>Interessados / pensando</span></div>
    <div class="tl-report-stat"><strong id="statTotal">0</strong><span>Total de inscrições</span></div>
  </section>

  <section class="tl-chart-grid">
    <article class="tl-chart-card">
      <h2>Relação vagas x confirmados</h2>
      <p>Compara a capacidade planejada do evento com o total de inscrições confirmadas.</p>
      <div class="tl-bar-row">
        <div class="tl-bar-label"><span>Vagas</span><span id="labelVagas">0</span></div>
        <div class="tl-bar-track"><div class="tl-bar-fill is-soft" id="barVagas" style="width:0%">0</div></div>
      </div>
      <div class="tl-bar-row">
        <div class="tl-bar-label"><span>Confirmados</span><span id="labelConfirmados">0</span></div>
        <div class="tl-bar-track"><div class="tl-bar-fill" id="barConfirmados" style="width:0%">0</div></div>
      </div>
    </article>

    <article class="tl-chart-card">
      <h2>Confirmados x interessados</h2>
      <p>Compara quem já está confirmado com quem marcou interesse ou está pensando.</p>
      <div class="tl-bar-row">
        <div class="tl-bar-label"><span>Confirmados</span><span id="labelConfirmados2">0</span></div>
        <div class="tl-bar-track"><div class="tl-bar-fill is-soft" id="barConfirmados2" style="width:0%">0</div></div>
      </div>
      <div class="tl-bar-row">
        <div class="tl-bar-label"><span>Interessados</span><span id="labelInteresse">0</span></div>
        <div class="tl-bar-track"><div class="tl-bar-fill" id="barInteresse" style="width:0%">0</div></div>
      </div>
    </article>
  </section>

  <section class="tl-card tl-card-pad">
    <div class="tl-section-head" style="margin-bottom:14px;">
      <div>
        <h2 style="margin:0; color:var(--azul-institucional);">Lista de inscritos</h2>
        <p class="tl-meta">Relação nominal para conferência administrativa e impressão.</p>
      </div>
    </div>
    <div class="tl-table-wrap">
      <table class="tl-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Matrícula</th>
            <th>Tipo</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="tabelaInscritos">
          <tr><td colspan="5">Carregando inscritos...</td></tr>
        </tbody>
      </table>
    </div>
  </section>
</main>

<script src="<?= tl_url('assets/js/techlounged.js') ?>"></script>
<script>
const idRegistro = <?= $idRegistro ?>;
const controllerPainel = '<?= BASE_URL ?>/services/registro_atividade_controle.php';

document.addEventListener('DOMContentLoaded', carregarRelatorio);

function numeroSeguro(valor) {
  const n = parseInt(valor, 10);
  return Number.isFinite(n) ? n : 0;
}

function percentual(valor, maximo) {
  if (!maximo || maximo <= 0) return 0;
  return Math.max(0, Math.min(100, Math.round((valor / maximo) * 100)));
}

function setBarra(idBarra, idLabel, valor, maximo) {
  const barra = document.getElementById(idBarra);
  const label = document.getElementById(idLabel);
  const pct = percentual(valor, maximo);
  barra.style.width = pct + '%';
  barra.textContent = valor;
  label.textContent = valor;
}

async function carregarRelatorio() {
  try {
    const [resEvento, resInscritos] = await Promise.all([
      fetch(`${controllerPainel}?acao=buscar&id=${idRegistro}`).then(r => r.json()),
      fetch(`${controllerPainel}?acao=listar_inscritos_evento&id_registro=${idRegistro}`).then(r => r.json())
    ]);

    if (!resEvento.sucesso) {
      throw new Error(resEvento.mensagem || 'Não foi possível carregar o evento.');
    }

    const evento = resEvento.dados;
    const inscritos = (resInscritos.sucesso && Array.isArray(resInscritos.dados)) ? resInscritos.dados : [];

    renderizarEvento(evento);
    renderizarResumo(evento, inscritos);
    renderizarTabela(inscritos);
  } catch (erro) {
    document.getElementById('tituloEvento').textContent = 'Erro ao carregar relatório';
    document.getElementById('descricaoEvento').textContent = erro.message || 'Confira o controller e tente novamente.';
    document.getElementById('tabelaInscritos').innerHTML = '<tr><td colspan="5">Erro ao carregar inscritos.</td></tr>';
  }
}

function renderizarEvento(evento) {
  const nome = evento.nome_projeto || 'Evento sem título';
  const tema = evento.tema_especifico || 'Tema geral';
  const espaco = evento.nome_espaco || 'Espaço não informado';
  const data = tlDataBR(evento.data_execucao);
  const status = evento.status || 'Planejado';

  document.getElementById('tituloEvento').textContent = nome;
  document.getElementById('descricaoEvento').textContent = `${tema} • ${data} • ${espaco}`;
  document.getElementById('dadosEvento').innerHTML = `
    <strong>Evento:</strong> ${tlTextoSeguro(nome)}<br>
    <strong>Tema:</strong> ${tlTextoSeguro(tema)}<br>
    <strong>Data:</strong> ${tlTextoSeguro(data)}${evento.data_finalizacao ? ' até ' + tlTextoSeguro(tlDataBR(evento.data_finalizacao)) : ''}<br>
    <strong>Espaço:</strong> ${tlTextoSeguro(espaco)}<br>
    <strong>Status:</strong> ${tlTextoSeguro(status)}
  `;
}

function renderizarResumo(evento, inscritos) {
  const vagas = numeroSeguro(evento.publico_previsto || evento.capacidade_maxima);
  const confirmados = inscritos.filter(i => i.status_inscricao === 'Confirmado').length;
  const interessados = inscritos.filter(i => i.tipo_inscricao === 'Pensando').length;
  const total = inscritos.length;

  document.getElementById('statVagas').textContent = vagas;
  document.getElementById('statConfirmados').textContent = confirmados;
  document.getElementById('statInteresse').textContent = interessados;
  document.getElementById('statTotal').textContent = total;

  const maxVagasConfirmados = Math.max(vagas, confirmados, 1);
  setBarra('barVagas', 'labelVagas', vagas, maxVagasConfirmados);
  setBarra('barConfirmados', 'labelConfirmados', confirmados, maxVagasConfirmados);

  const maxConfirmadosInteresse = Math.max(confirmados, interessados, 1);
  setBarra('barConfirmados2', 'labelConfirmados2', confirmados, maxConfirmadosInteresse);
  setBarra('barInteresse', 'labelInteresse', interessados, maxConfirmadosInteresse);
}

function renderizarTabela(inscritos) {
  const corpo = document.getElementById('tabelaInscritos');

  if (!inscritos.length) {
    corpo.innerHTML = '<tr><td colspan="5">Nenhum inscrito encontrado para este evento.</td></tr>';
    return;
  }

  corpo.innerHTML = inscritos.map((inscrito, indice) => `
    <tr>
      <td>${indice + 1}</td>
      <td><strong>${tlTextoSeguro(inscrito.nome || 'Sem nome')}</strong></td>
      <td>${tlTextoSeguro(inscrito.matricula || '-')}</td>
      <td>${tlTextoSeguro(inscrito.tipo_inscricao || '-')}</td>
      <td><span class="tl-badge">${tlTextoSeguro(inscrito.status_inscricao || '-')}</span></td>
    </tr>
  `).join('');
}
</script>
</body>
</html>
