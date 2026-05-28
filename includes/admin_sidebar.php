<?php
$paginaAtual = basename($_SERVER['PHP_SELF']);
$linksAdmin = [
  'atividade.php' => 'Atividades',
  'registros.php' => 'Registros de eventos',
  'cine_biblioteca.php' => 'Cine Biblioteca',
  'eixo.php' => 'Eixos',
  'espaco.php' => 'Espaços',
  'periodicidade.php' => 'Periodicidades',
  'publico_alvo.php' => 'Público-alvo',
];
?>
<div class="tl-layout">
  <aside class="tl-sidebar">
    <strong style="display:block; margin:0 0 14px;">Gestão TechLounged</strong>
    <?php foreach ($linksAdmin as $arquivo => $rotulo): ?>
      <a class="<?= $paginaAtual === $arquivo ? 'ativo' : '' ?>" href="<?= tl_url('views/admin/' . $arquivo) ?>"><?= $rotulo ?></a>
    <?php endforeach; ?>
    <hr style="border:0; border-top:1px solid rgba(255,255,255,.18); margin:16px 0;">
    <a href="<?= tl_url('views/eventos.php') ?>">Ver página pública</a>
  </aside>
