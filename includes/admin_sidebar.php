<?php
$paginaAtual = basename($_SERVER['PHP_SELF']);
$menuPrincipal = [
  'registros.php' => ['rotulo' => 'Registro de eventos', 'icone' => '📅'],
  'cine_biblioteca.php' => ['rotulo' => 'Cine Biblioteca', 'icone' => '🎬'],
  'gerenciamento_sistema.php' => ['rotulo' => 'Gerenciamento do sistema', 'icone' => '⚙️'],
];
$paginasGerenciamento = ['atividade.php','eixo.php','espaco.php','periodicidade.php','publico_alvo.php','usuarios.php','gerenciamento_sistema.php'];
?>
<div class="tl-layout">
  <aside class="tl-sidebar">
    <div class="tl-sidebar-brand">
      <strong>Gestão TechLounged</strong>
      <small>Painel administrativo</small>
    </div>

    <?php foreach ($menuPrincipal as $arquivo => $item): ?>
      <?php
        $ativo = $paginaAtual === $arquivo;
        if ($arquivo === 'gerenciamento_sistema.php' && in_array($paginaAtual, $paginasGerenciamento, true)) {
          $ativo = true;
        }
      ?>
      <a class="<?= $ativo ? 'ativo' : '' ?>" href="<?= tl_url('views/admin/' . $arquivo) ?>">
        <span><?= $item['icone'] ?></span>
        <span><?= $item['rotulo'] ?></span>
      </a>
    <?php endforeach; ?>

    <hr class="tl-sidebar-separador">

    <button class="tl-sidebar-theme" type="button" onclick="tlAlternarTema()" data-tl-theme-toggle>
      🌙 Modo escuro
    </button>

    <a href="<?= tl_url('views/eventos.php') ?>">🌐 Ver página pública</a>
  </aside>
