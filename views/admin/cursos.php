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
  <title>Cursos | TechLounged</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/techlounged.css">
</head>
<body>
<?php include(__DIR__ . '/../../includes/topo.php'); ?>
<?php include(__DIR__ . '/../../includes/admin_sidebar.php'); ?>

<main class="tl-main">
  <div class="tl-admin-top">
    <div>
      <h1>Cursos</h1>
      <p class="tl-meta">Gerencie os cursos vinculados aos usuários do sistema.</p>
    </div>
    <a class="tl-btn tl-btn-secondary" href="<?= tl_url('views/admin/gerenciamento_sistema.php') ?>">Voltar ao gerenciamento</a>
  </div>

  <section class="tl-card tl-card-pad" style="margin-bottom:18px;">
    <h2 id="tituloFormulario" style="color:var(--azul-institucional); margin-top:0;">Novo curso</h2>

    <form id="formCurso" class="tl-form-grid">
      <input type="hidden" id="id" name="id">

      <div class="tl-field">
        <label>Nome do curso *</label>
        <input type="text" id="nome_curso" name="nome_curso" maxlength="50" required placeholder="Ex: Técnico em Desenvolvimento de Sistemas">
      </div>

      <div class="tl-field tl-full">
        <label>Descrição</label>
        <textarea id="descricao" name="descricao" rows="3" placeholder="Descrição opcional do curso"></textarea>
      </div>

      <div class="tl-actions tl-full">
        <button class="tl-btn tl-btn-primary" id="btnSalvar" type="submit">Salvar curso</button>
        <button class="tl-btn tl-btn-secondary" id="btnCancelar" type="button" style="display:none" onclick="resetarFormulario()">Cancelar edição</button>
      </div>
    </form>
  </section>

  <section class="tl-table-wrap">
    <table class="tl-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Curso</th>
          <th>Descrição</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody id="tabelaCorpo"></tbody>
    </table>
  </section>
</main>
</div>

<script src="<?= tl_url('assets/js/techlounged.js') ?>"></script>
<script>
const urlController = '<?= BASE_URL ?>/services/curso_controle.php';

document.addEventListener('DOMContentLoaded', listarCursos);

function listarCursos() {
  const corpo = document.getElementById('tabelaCorpo');
  corpo.innerHTML = '<tr><td colspan="4">Carregando cursos...</td></tr>';

  fetch(`${urlController}?acao=listar`)
    .then(r => r.json())
    .then(res => {
      corpo.innerHTML = '';

      if (!res.sucesso) {
        corpo.innerHTML = `<tr><td colspan="4">${tlTextoSeguro(res.mensagem || 'Erro ao carregar cursos.')}</td></tr>`;
        return;
      }

      if (!res.dados.length) {
        corpo.innerHTML = '<tr><td colspan="4">Nenhum curso cadastrado.</td></tr>';
        return;
      }

      res.dados.forEach(curso => {
        corpo.innerHTML += `
          <tr>
            <td>${parseInt(curso.id)}</td>
            <td><strong>${tlTextoSeguro(curso.nome_curso)}</strong></td>
            <td>${tlTextoSeguro(curso.descricao || '-')}</td>
            <td>
              <div class="tl-row-actions">
                <button class="tl-btn tl-btn-secondary tl-btn-small" type="button" onclick="editarCurso(${parseInt(curso.id)})">Editar</button>
                <button class="tl-btn tl-btn-danger tl-btn-small" type="button" onclick="deletarCurso(${parseInt(curso.id)})">Excluir</button>
              </div>
            </td>
          </tr>`;
      });
    })
    .catch(() => {
      corpo.innerHTML = '<tr><td colspan="4">Erro ao carregar cursos. Verifique o arquivo services/curso_controle.php.</td></tr>';
    });
}

document.getElementById('formCurso').addEventListener('submit', function(e) {
  e.preventDefault();

  const id = document.getElementById('id').value;
  const acao = id ? 'atualizar' : 'criar';

  fetch(`${urlController}?acao=${acao}`, {
    method: 'POST',
    body: new FormData(this)
  })
    .then(r => r.json())
    .then(res => {
      alert(res.mensagem);
      if (res.sucesso) {
        resetarFormulario();
        listarCursos();
      }
    });
});

function editarCurso(id) {
  fetch(`${urlController}?acao=buscar&id=${id}`)
    .then(r => r.json())
    .then(res => {
      if (!res.sucesso) return alert(res.mensagem || 'Curso não encontrado.');

      document.getElementById('id').value = res.dados.id || '';
      document.getElementById('nome_curso').value = res.dados.nome_curso || '';
      document.getElementById('descricao').value = res.dados.descricao || '';
      document.getElementById('tituloFormulario').textContent = 'Editar curso';
      document.getElementById('btnSalvar').textContent = 'Atualizar curso';
      document.getElementById('btnCancelar').style.display = 'inline-flex';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function deletarCurso(id) {
  if (!confirm('Deseja realmente excluir este curso?')) return;

  fetch(`${urlController}?acao=deletar&id=${id}`)
    .then(r => r.json())
    .then(res => {
      alert(res.mensagem);
      if (res.sucesso) listarCursos();
    });
}

function resetarFormulario() {
  document.getElementById('formCurso').reset();
  document.getElementById('id').value = '';
  document.getElementById('tituloFormulario').textContent = 'Novo curso';
  document.getElementById('btnSalvar').textContent = 'Salvar curso';
  document.getElementById('btnCancelar').style.display = 'none';
}
</script>
</body>
</html>
