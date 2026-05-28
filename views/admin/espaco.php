<?php require_once(__DIR__ . '/../../middleware/permissao.php'); verificarPermissao(['Administrador', 'Bibliotecário', 'Bibliotecario']); require_once(__DIR__ . '/../../config/app.php'); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Espaços | TechLounged</title><link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/techlounged.css"></head>
<body>
<?php include(__DIR__ . '/../../includes/topo.php'); ?>
<?php include(__DIR__ . '/../../includes/admin_sidebar.php'); ?>
<main class="tl-main">
  <div class="tl-admin-top"><div><h1>Espaços</h1><p class="tl-meta">Cadastro administrativo restrito a administrador e bibliotecário.</p></div></div>
  <section class="tl-card tl-card-pad" style="margin-bottom:18px;">
    <form id="formCrud" class="tl-form-grid">
      <input type="hidden" id="id" name="id">
      <div class="tl-field"><label>Nome do espaço</label><input type="text" id="nome_espaco" name="nome_espaco" placeholder="Ex: Auditório" required></div>
      <div class="tl-field"><label>Capacidade máxima</label><input type="number" id="capacidade_maxima" name="capacidade_maxima" placeholder="Ex: 30" required></div>
      <div class="tl-actions tl-full"><button class="tl-btn tl-btn-primary" type="submit" id="btnSalvar">Salvar</button><button class="tl-btn tl-btn-secondary" type="button" id="btnCancelar" style="display:none" onclick="resetarFormulario()">Cancelar edição</button></div>
    </form>
  </section>
  <section class="tl-table-wrap"><table class="tl-table"><thead><tr><th>ID</th><th>Espaço</th><th>Capacidade</th><th>Ações</th></tr></thead><tbody id="tabelaCorpo"></tbody></table></section>
</main></div>
<script src="<?= tl_url('assets/js/techlounged.js') ?>"></script>
<script>
const urlController = '<?= BASE_URL ?>/services/espaco_controle.php';
const colunas = [["id", "ID"], ["nome_espaco", "Espaço"], ["capacidade_maxima", "Capacidade"]];
document.addEventListener('DOMContentLoaded', listar);
document.getElementById('formCrud').addEventListener('submit', function(e) {
  e.preventDefault();
  const acao = document.getElementById('id').value ? 'atualizar' : 'criar';
  fetch(`${urlController}?acao=${acao}`, { method:'POST', body:new FormData(this) })
    .then(res => res.json()).then(res => { alert(res.mensagem); if(res.sucesso) { resetarFormulario(); listar(); } });
});
function listar() {
  fetch(`${urlController}?acao=listar`).then(res => res.json()).then(res => {
    const corpo = document.getElementById('tabelaCorpo'); corpo.innerHTML = '';
    if(!res.sucesso || !res.dados.length) { corpo.innerHTML = `<tr><td colspan="${colunas.length + 1}">Nenhum registro encontrado.</td></tr>`; return; }
    res.dados.forEach(item => {
      corpo.innerHTML += `<tr>${colunas.map(c => `<td>${tlTextoSeguro(item[c[0]] ?? '')}</td>`).join('')}<td><button class="tl-btn tl-btn-secondary" onclick="editar(${item.id})">Editar</button> <button class="tl-btn tl-btn-danger" onclick="deletar(${item.id})">Excluir</button></td></tr>`;
    });
  });
}
function editar(id) {
  fetch(`${urlController}?acao=buscar&id=${id}`).then(res => res.json()).then(res => {
    if(!res.sucesso) return alert(res.mensagem);
    Object.keys(res.dados).forEach(k => { const el = document.getElementById(k); if(el) el.value = res.dados[k] ?? ''; });
    document.getElementById('btnCancelar').style.display = 'inline-flex';
    document.getElementById('btnSalvar').innerText = 'Atualizar';
  });
}
function deletar(id) {
  if(!confirm('Deseja realmente excluir este registro?')) return;
  fetch(`${urlController}?acao=deletar&id=${id}`).then(res => res.json()).then(res => { alert(res.mensagem); if(res.sucesso) listar(); });
}
function resetarFormulario() {
  document.getElementById('formCrud').reset(); document.getElementById('id').value = '';
  document.getElementById('btnCancelar').style.display = 'none'; document.getElementById('btnSalvar').innerText = 'Salvar';
}
</script>
</body></html>
