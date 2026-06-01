<?php
require_once(__DIR__ . '/../../middleware/permissao.php');
verificarPermissao(['Administrador', 'Bibliotecário', 'Bibliotecario']);
require_once(__DIR__ . '/../../config/app.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registros de eventos | TechLounged</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/techlounged.css">
</head>
<body>
<?php include(__DIR__ . '/../../includes/topo.php'); ?>
<?php include(__DIR__ . '/../../includes/admin_sidebar.php'); ?>

<main class="tl-main">
  <div class="tl-admin-top">
    <div>
      <h1>Registros de eventos</h1>
      <p class="tl-meta">Crie execuções reais das atividades matriz e controle datas, vagas, espaços e status.</p>
    </div>
    <a class="tl-btn tl-btn-secondary" href="<?= tl_url('views/eventos.php') ?>">Ver agenda pública</a>
  </div>

  <section class="tl-card tl-card-pad" style="margin-bottom:20px;">
    <h2 id="tituloFormulario" style="color:var(--azul-institucional); margin-top:0;">Novo registro de evento</h2>

    <form id="formRegistro" class="tl-form-grid">
      <input type="hidden" id="id" name="id">

      <div class="tl-field">
        <label>Atividade *</label>
        <select id="id_atividade" name="id_atividade" required>
          <option value="">Carregando...</option>
        </select>
      </div>

      <div class="tl-field">
        <label>Espaço *</label>
        <select id="id_espaco" name="id_espaco" required>
          <option value="">Carregando...</option>
        </select>
      </div>

      <div class="tl-field tl-full">
        <label>Tema específico</label>
        <input type="text" id="tema_especifico" name="tema_especifico" placeholder="Ex: Oficina de Robótica Básica">
      </div>

      <div class="tl-field">
        <label>Data de execução *</label>
        <input type="date" id="data_execucao" name="data_execucao" required>
      </div>

      <div class="tl-field">
        <label>Data de finalização</label>
        <input type="date" id="data_finalizacao" name="data_finalizacao">
      </div>

      <div class="tl-field">
        <label>Status</label>
        <select id="status" name="status">
          <option value="Planejado">Planejado</option>
          <option value="Concluído">Concluído</option>
          <option value="Cancelado">Cancelado</option>
        </select>
      </div>

      <div class="tl-field">
        <label>Público previsto</label>
        <input type="number" id="publico_previsto" name="publico_previsto" min="0">
      </div>

      <div class="tl-field">
        <label>Público realizado</label>
        <input type="number" id="publico_realizado" name="publico_realizado" min="0" value="0">
      </div>

      <div class="tl-field">
        <label>URL da imagem</label>
        <input type="url" id="url_imagem" name="url_imagem" placeholder="https://...">
      </div>

      <div class="tl-field tl-full">
        <label>
          <input type="checkbox" id="confirm_auto" name="confirm_auto" value="1" checked>
          Confirmar inscrições automaticamente se houver vagas
        </label>
      </div>

      <div class="tl-actions tl-full">
        <button class="tl-btn tl-btn-primary" type="submit" id="btnSalvar">Salvar registro</button>
        <button class="tl-btn tl-btn-secondary" type="button" onclick="limparFormulario()">Cancelar edição</button>
      </div>
    </form>
  </section>

  <section class="tl-card tl-card-pad">
    <div class="tl-section-head" style="margin-bottom:14px;">
      <div>
        <h2 style="margin:0; color:var(--azul-institucional);">Eventos registrados</h2>
        <p class="tl-meta">Use os filtros para localizar registros cadastrados.</p>
      </div>
    </div>

    <div class="tl-filter" style="margin-bottom:16px;">
      <div class="tl-field"><label>Data inicial</label><input type="date" id="f_data_execucao"></div>
      <div class="tl-field"><label>Data final</label><input type="date" id="f_data_finalizacao"></div>
      <div class="tl-field"><label>Status</label><select id="f_status"><option value="">Todos</option><option value="Planejado">Planejado</option><option value="Concluído">Concluído</option><option value="Cancelado">Cancelado</option></select></div>
      <button class="tl-btn tl-btn-primary" type="button" onclick="listarRegistros()">Filtrar</button>
    </div>

    <div class="tl-table-wrap">
      <table class="tl-table">
        <thead>
          <tr>
            <th>Tema</th>
            <th>Atividade</th>
            <th>Espaço</th>
            <th>Data</th>
            <th>Status</th>
            <th>Publico Previsto</th>
            <th>Vagas</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody id="tabelaRegistros"></tbody>
      </table>
    </div>
  </section>
</main>
</div>

<script src="<?= tl_url('assets/js/techlounged.js') ?>"></script>
<script>
const controllerPainel = '<?= BASE_URL ?>/services/registro_atividade_controle.php';

document.addEventListener('DOMContentLoaded', () => {
  carregarSelectsAuxiliares();
  listarRegistros();
});

function obterQueryFiltros() {
  return `&f_data_execucao=${document.getElementById('f_data_execucao').value}&f_data_finalizacao=${document.getElementById('f_data_finalizacao').value}&f_status=${document.getElementById('f_status').value}`;
}

function carregarSelectsAuxiliares() {
  fetch(`${controllerPainel}?acao=carregar_selects_cadastro`)
    .then(res => res.json())
    .then(res => {
      if (!res.sucesso) {
        alert(res.mensagem || 'Erro ao carregar listas auxiliares.');
        return;
      }

      const selectAtiv = document.getElementById('id_atividade');
      const selectEsp = document.getElementById('id_espaco');

      selectAtiv.innerHTML = '<option value="">Selecione uma atividade matriz...</option>';
      selectEsp.innerHTML = '<option value="">Selecione o espaço...</option>';

      res.dados.atividades.forEach(ativ => {
        selectAtiv.innerHTML += `<option value="${ativ.id}">${tlTextoSeguro(ativ.nome_projeto)}</option>`;
      });

      res.dados.espacos.forEach(esp => {
        selectEsp.innerHTML += `<option value="${esp.id}">${tlTextoSeguro(esp.nome_espaco)} (Máx: ${tlTextoSeguro(esp.capacidade_maxima || 'não informado')})</option>`;
      });
    });
}

function calcularVagasDisponiveis(item) {
  const capacidade = parseInt(item.capacidade_maxima || item.publico_previsto || 0);
  const confirmados = parseInt(item.total_confirmados || 0);

  if (!capacidade || capacidade <= 0) {
    return 'A definir';
  }

  const disponiveis = capacidade - confirmados;
  return Math.max(disponiveis, 0);
}

function listarRegistros() {
  const corpo = document.getElementById('tabelaRegistros');
  corpo.innerHTML = '<tr><td colspan="8">Carregando registros...</td></tr>';

  fetch(`${controllerPainel}?acao=listar_admin${obterQueryFiltros()}`)
    .then(res => res.json())
    .then(res => {
      corpo.innerHTML = '';

      if (!res.sucesso || !res.dados.length) {
        corpo.innerHTML = '<tr><td colspan="8">Nenhum registro encontrado.</td></tr>';
        return;
      }

      res.dados.forEach(item => {
        corpo.innerHTML += `
          <tr>
            <td><strong>${tlTextoSeguro(item.tema_especifico || 'Geral')}</strong></td>
            <td>${tlTextoSeguro(item.nome_projeto)}</td>
            <td>${tlTextoSeguro(item.nome_espaco || 'Não informado')}</td>
            <td>${tlDataBR(item.data_execucao)}</td>
            <td><span class="tl-badge">${tlTextoSeguro(item.status || 'Planejado')}</span></td>
            <td>${tlTextoSeguro(item.publico_previsto ||  'A definir')}</td>
            <td>
              <strong>${calcularVagasDisponiveis(item)}</strong>
              <small class="tl-meta" style="display:block;">Confirmados: ${parseInt(item.total_confirmados || 0)}</small>
            </td>
            <td>
              <div class="tl-row-actions">
                <button class="tl-btn tl-btn-secondary tl-btn-small" type="button" onclick="editarRegistro(${parseInt(item.id)})">Editar</button>
                <a class="tl-btn tl-btn-primary tl-btn-small" href="<?= tl_url('views/admin/relatorio_inscritos.php') ?>?id=${parseInt(item.id)}" target="_blank">Relatório</a>
              </div>
            </td>
          </tr>
        `;
      });
    })
    .catch(() => {
      corpo.innerHTML = '<tr><td colspan="8">Erro ao carregar registros. Confira o controller registro_atividade_controle.php.</td></tr>';
    });
}

document.getElementById('formRegistro').addEventListener('submit', function(e) {
  e.preventDefault();

  const id = document.getElementById('id').value;
  const acao = id ? 'atualizar' : 'criar';

  fetch(`${controllerPainel}?acao=${acao}`, {
    method: 'POST',
    body: new FormData(this)
  })
    .then(res => res.json())
    .then(res => {
      alert(res.mensagem);
      if (res.sucesso) {
        limparFormulario();
        listarRegistros();
      }
    });
});

function editarRegistro(idRegistro) {
  fetch(`${controllerPainel}?acao=buscar&id=${idRegistro}`)
    .then(res => res.json())
    .then(res => {
      if (!res.sucesso) {
        alert(res.mensagem || 'Erro ao buscar registro.');
        return;
      }

      document.getElementById('tituloFormulario').innerText = 'Editar registro de evento';
      document.getElementById('btnSalvar').innerText = 'Atualizar registro';

      const campos = ['id','id_atividade','id_espaco','tema_especifico','data_execucao','data_finalizacao','status','publico_previsto','publico_realizado','url_imagem'];
      campos.forEach(campo => {
        const el = document.getElementById(campo);
        if (el) el.value = res.dados[campo] || '';
      });

      document.getElementById('confirm_auto').checked = res.dados.confirm_auto == 1;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function limparFormulario() {
  document.getElementById('formRegistro').reset();
  document.getElementById('id').value = '';
  document.getElementById('tituloFormulario').innerText = 'Novo registro de evento';
  document.getElementById('btnSalvar').innerText = 'Salvar registro';
}
</script>
</body>
</html>
