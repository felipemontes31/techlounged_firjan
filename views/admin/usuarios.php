<?php
require_once(__DIR__ . '/../../middleware/permissao.php');
verificarPermissao(['Administrador']);
require_once(__DIR__ . '/../../config/app.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuários | TechLounged</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/techlounged.css">
</head>
<body>
<?php include(__DIR__ . '/../../includes/topo.php'); ?>
<?php include(__DIR__ . '/../../includes/admin_sidebar.php'); ?>

<main class="tl-main">
  <div class="tl-admin-top">
    <div>
      <h1>Usuários</h1>
      <p class="tl-meta">Gerencie dados cadastrais, curso, status e perfil de acesso dos usuários.</p>
    </div>
    <a class="tl-btn tl-btn-secondary" href="<?= tl_url('views/admin/gerenciamento_sistema.php') ?>">Voltar ao gerenciamento</a>
  </div>

  <section class="tl-card tl-card-pad" style="margin-bottom:18px;">
    <h2 id="tituloFormulario" style="color:var(--azul-institucional); margin-top:0;">Novo usuário</h2>
    <form id="formUsuario" class="tl-form-grid">
      <input type="hidden" id="id" name="id">

      <div class="tl-field"><label>Nome *</label><input id="nome" name="nome" maxlength="30" required></div>
      <div class="tl-field"><label>Sobrenome</label><input id="sobrenome" name="sobrenome" maxlength="100"></div>
      <div class="tl-field"><label>E-mail *</label><input type="email" id="email" name="email" required></div>
      <div class="tl-field"><label>Matrícula</label><input id="matricula" name="matricula" maxlength="10"></div>

      <div class="tl-field">
        <label>Curso</label>
        <select id="id_curso" name="id_curso">
          <option value="">Carregando...</option>
        </select>
      </div>

      <div class="tl-field">
        <label>Sexo</label>
        <select id="sexo" name="sexo">
          <option value="Prefiro não informar">Prefiro não informar</option>
          <option value="Masculino">Masculino</option>
          <option value="Feminino">Feminino</option>
        </select>
      </div>

      <div class="tl-field">
        <label>Perfil/Função *</label>
        <select id="id_funcao" name="id_funcao" required>
          <option value="">Carregando...</option>
        </select>
      </div>

      <div class="tl-field">
        <label>Status</label>
        <select id="ativo" name="ativo">
          <option value="1">Ativo</option>
          <option value="0">Inativo</option>
        </select>
      </div>

      <div class="tl-field">
        <label>Senha <span id="senhaAjuda" style="font-weight:600; text-transform:none; color:var(--texto-suave);">*</span></label>
        <input type="password" id="senha" name="senha" autocomplete="new-password" placeholder="Obrigatória ao criar; opcional ao editar">
      </div>

      <div class="tl-actions tl-full">
        <button class="tl-btn tl-btn-primary" id="btnSalvar" type="submit">Salvar usuário</button>
        <button class="tl-btn tl-btn-secondary" id="btnCancelar" type="button" style="display:none" onclick="resetarFormulario()">Cancelar edição</button>
      </div>
    </form>
  </section>

  <section class="tl-table-wrap">
    <table class="tl-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuário</th>
          <th>E-mail</th>
          <th>Matrícula</th>
          <th>Curso</th>
          <th>Perfil</th>
          <th>Status</th>
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
const urlController = '<?= BASE_URL ?>/services/usuario_controle.php';

document.addEventListener('DOMContentLoaded', () => {
  carregarFuncoes();
  carregarCursos();
  listarUsuarios();
});

function carregarFuncoes() {
  fetch(`${urlController}?acao=listar_funcoes`)
    .then(r => r.json())
    .then(res => {
      const select = document.getElementById('id_funcao');
      select.innerHTML = '<option value="">Selecione...</option>';
      if (!res.sucesso) return alert(res.mensagem);
      res.dados.forEach(f => {
        select.innerHTML += `<option value="${f.id}">${tlTextoSeguro(f.nome_funcao)}</option>`;
      });
    });
}

function carregarCursos() {
  fetch(`${urlController}?acao=listar_cursos`)
    .then(r => r.json())
    .then(res => {
      const select = document.getElementById('id_curso');
      select.innerHTML = '<option value="">Sem curso vinculado</option>';
      if (!res.sucesso) return alert(res.mensagem);
      res.dados.forEach(curso => {
        select.innerHTML += `<option value="${curso.id}">${tlTextoSeguro(curso.nome_curso)}</option>`;
      });
    });
}

function listarUsuarios() {
  fetch(`${urlController}?acao=listar`)
    .then(r => r.json())
    .then(res => {
      const corpo = document.getElementById('tabelaCorpo');
      corpo.innerHTML = '';
      if (!res.sucesso) {
        corpo.innerHTML = `<tr><td colspan="8">${tlTextoSeguro(res.mensagem)}</td></tr>`;
        return;
      }
      if (!res.dados.length) {
        corpo.innerHTML = '<tr><td colspan="8">Nenhum usuário encontrado.</td></tr>';
        return;
      }
      res.dados.forEach(u => {
        const nomeCompleto = `${u.nome || ''} ${u.sobrenome || ''}`.trim();
        corpo.innerHTML += `
          <tr>
            <td>${u.id}</td>
            <td><strong>${tlTextoSeguro(nomeCompleto)}</strong></td>
            <td>${tlTextoSeguro(u.email)}</td>
            <td>${tlTextoSeguro(u.matricula || '-')}</td>
            <td>${tlTextoSeguro(u.nome_curso || '-')}</td>
            <td>${tlTextoSeguro(u.nome_funcao || u.funcao || u.id_funcao)}</td>
            <td>${u.ativo == 1 ? '<span class="tl-chip">Ativo</span>' : '<span class="tl-chip off">Inativo</span>'}</td>
            <td>
              <div class="tl-row-actions">
                <button class="tl-btn tl-btn-secondary tl-btn-small" onclick="editarUsuario(${parseInt(u.id)})">Editar</button>
                <button class="tl-btn tl-btn-danger tl-btn-small" onclick="excluirUsuario(${parseInt(u.id)})">Excluir</button>
              </div>
            </td>
          </tr>`;
      });
    });
}

document.getElementById('formUsuario').addEventListener('submit', function(e) {
  e.preventDefault();
  const id = document.getElementById('id').value;
  const acao = id ? 'editar' : 'criar';
  const senha = document.getElementById('senha');

  if (!id && !senha.value.trim()) {
    alert('A senha é obrigatória para criar um usuário.');
    senha.focus();
    return;
  }

  fetch(`${urlController}?acao=${acao}`, { method: 'POST', body: new FormData(this) })
    .then(r => r.json())
    .then(res => {
      alert(res.mensagem);
      if (res.sucesso) {
        resetarFormulario();
        listarUsuarios();
      }
    });
});

function editarUsuario(id) {
  fetch(`${urlController}?acao=buscar&id=${id}`)
    .then(r => r.json())
    .then(res => {
      if (!res.sucesso) return alert(res.mensagem);
      const u = res.dados;
      ['id','nome','sobrenome','email','matricula','sexo','id_funcao','id_curso','ativo'].forEach(campo => {
        const el = document.getElementById(campo);
        if (el) el.value = u[campo] ?? '';
      });
      document.getElementById('senha').value = '';
      document.getElementById('senha').required = false;
      document.getElementById('senhaAjuda').textContent = '(opcional ao editar)';
      document.getElementById('tituloFormulario').textContent = 'Editar usuário';
      document.getElementById('btnSalvar').textContent = 'Atualizar usuário';
      document.getElementById('btnCancelar').style.display = 'inline-flex';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function excluirUsuario(id) {
  if (!confirm('Deseja realmente excluir este usuário?')) return;
  const formData = new FormData();
  formData.append('id', id);
  fetch(`${urlController}?acao=excluir`, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.mensagem);
      if (res.sucesso) listarUsuarios();
    });
}

function resetarFormulario() {
  document.getElementById('formUsuario').reset();
  document.getElementById('id').value = '';
  document.getElementById('senha').required = false;
  document.getElementById('senhaAjuda').textContent = '*';
  document.getElementById('tituloFormulario').textContent = 'Novo usuário';
  document.getElementById('btnSalvar').textContent = 'Salvar usuário';
  document.getElementById('btnCancelar').style.display = 'none';
}
</script>
</body>
</html>
