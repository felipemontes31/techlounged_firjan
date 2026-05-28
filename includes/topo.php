<?php
require_once(__DIR__ . '/../config/app.php');
if (session_status() === PHP_SESSION_NONE) session_start();

$usuarioLogado = $_SESSION['usuario'] ?? null;
$funcaoUsuario = $usuarioLogado['funcao'] ?? $usuarioLogado['nome_funcao'] ?? null;
$idFuncao = $usuarioLogado['id_funcao'] ?? null;

if (!function_exists('tl_usuario_eh_admin')) {
    function tl_usuario_eh_admin() {
        global $funcaoUsuario, $idFuncao;
        return in_array($funcaoUsuario, ['Administrador', 'Bibliotecário', 'Bibliotecario']) || in_array((string)$idFuncao, ['1', '2']);
    }
}

if (!function_exists('tl_usuario_eh_comum')) {
    function tl_usuario_eh_comum() {
        global $usuarioLogado;
        return !empty($usuarioLogado) && !tl_usuario_eh_admin();
    }
}

if (!function_exists('tl_url')) {
    function tl_url($caminho) {
        return BASE_URL . '/' . ltrim($caminho, '/');
    }
}
?>
<header class="tl-header">
  <div class="tl-container tl-nav">
    <a class="tl-brand" href="<?= tl_url('views/eventos.php') ?>">
      <span class="tl-brand-mark">TL</span>
      <span>TechLounged<small>Biblioteca • Eventos • Inscrições</small></span>
    </a>

    <div class="tl-nav-actions"><button class="tl-theme-toggle" type="button" onclick="tlAlternarTema()" data-tl-theme-toggle>🌙</button><button class="tl-mobile-toggle" type="button" onclick="tlAlternarMenu()">☰ Menu</button></div>

    <nav class="tl-menu" data-tl-menu>
      <a href="<?= tl_url('views/eventos.php') ?>">Eventos</a>
      <?php if ($usuarioLogado): ?>
        <a href="<?= tl_url('views/minhas_inscricoes.php') ?>">Minhas inscrições</a>
        <a href="<?= tl_url('views/perfil.php') ?>">Meu perfil</a>
      <?php endif; ?>
      <?php if (tl_usuario_eh_admin()): ?>
        <a href="<?= tl_url('views/admin/registros.php') ?>">Administração</a>
      <?php endif; ?>
      <?php if ($usuarioLogado): ?>
        <a href="<?= tl_url('services/auth/logout.php') ?>">Sair</a>
      <?php else: ?>
        <a href="<?= tl_url('views/login.php') ?>">Entrar</a>
        <a class="ativo" href="<?= tl_url('views/cadastro.php') ?>">Cadastrar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
