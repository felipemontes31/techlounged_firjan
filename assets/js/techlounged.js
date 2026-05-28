function tlAlternarMenu() {
  const menu = document.querySelector('[data-tl-menu]');
  if (menu) menu.classList.toggle('aberto');
}

function tlFecharModais() {
  document.querySelectorAll('.tl-modal').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.tl-modal-overlay').forEach(el => el.style.display = 'none');
}

function tlAbrirModal(id) {
  const overlay = document.querySelector('.tl-modal-overlay');
  const modal = document.getElementById(id);
  if (overlay) overlay.style.display = 'block';
  if (modal) modal.style.display = 'block';
}

function tlTextoSeguro(valor, fallback = '') {
  if (valor === null || valor === undefined || valor === '') return fallback;
  return String(valor)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function tlDataBR(data) {
  if (!data) return 'Data a definir';
  const partes = String(data).split('-');
  if (partes.length !== 3) return data;
  return `${partes[2]}/${partes[1]}/${partes[0]}`;
}
