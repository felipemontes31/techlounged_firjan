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
  <title>Gerenciamento do sistema | TechLounged</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/techlounged.css">
</head>
<body>
<?php include(__DIR__ . '/../../includes/topo.php'); ?>
<?php include(__DIR__ . '/../../includes/admin_sidebar.php'); ?>

<main class="tl-main">
  <div class="tl-admin-top">
    <div>
      <h1>Gerenciamento do sistema</h1>
      <p class="tl-meta">Centralize aqui as tabelas de apoio, atividades matriz, espaços e perfis de usuários.</p>
    </div>
  </div>

  <section class="tl-system-grid">
    <article class="tl-card tl-system-card">
      <span class="tl-system-icon">📚</span>
      <h3>Atividades</h3>
      <p>Cadastre os projetos base que depois serão transformados em registros de eventos.</p>
      <div class="tl-actions"><a class="tl-btn tl-btn-primary" href="<?= tl_url('views/admin/atividade.php') ?>">Gerenciar</a></div>
    </article>

    <article class="tl-card tl-system-card">
      <span class="tl-system-icon">🧭</span>
      <h3>Eixos</h3>
      <p>Organize as atividades por eixo tecnológico, cultural ou pedagógico.</p>
      <div class="tl-actions"><a class="tl-btn tl-btn-primary" href="<?= tl_url('views/admin/eixo.php') ?>">Gerenciar</a></div>
    </article>

    <article class="tl-card tl-system-card">
      <span class="tl-system-icon">🏫</span>
      <h3>Espaços</h3>
      <p>Controle locais, salas, auditórios e capacidade máxima dos eventos.</p>
      <div class="tl-actions"><a class="tl-btn tl-btn-primary" href="<?= tl_url('views/admin/espaco.php') ?>">Gerenciar</a></div>
    </article>

    <article class="tl-card tl-system-card">
      <span class="tl-system-icon">🔁</span>
      <h3>Periodicidades</h3>
      <p>Mantenha os tipos de recorrência usados no cadastro das atividades matriz.</p>
      <div class="tl-actions"><a class="tl-btn tl-btn-primary" href="<?= tl_url('views/admin/periodicidade.php') ?>">Gerenciar</a></div>
    </article>

    <article class="tl-card tl-system-card">
      <span class="tl-system-icon">👥</span>
      <h3>Público-alvo</h3>
      <p>Defina os públicos para segmentar atividades e facilitar a organização da biblioteca.</p>
      <div class="tl-actions"><a class="tl-btn tl-btn-primary" href="<?= tl_url('views/admin/publico_alvo.php') ?>">Gerenciar</a></div>
    </article>

    <article class="tl-card tl-system-card">
      <span class="tl-system-icon">🎓</span>
      <h3>Cursos</h3>
      <p>Cadastre os cursos que podem ser vinculados aos usuários e suas matrículas.</p>
      <div class="tl-actions"><a class="tl-btn tl-btn-primary" href="<?= tl_url('views/admin/cursos.php') ?>">Gerenciar</a></div>
    </article>

    <?php $funcao = $_SESSION['usuario']['funcao'] ?? ''; ?>
    <?php if ($funcao === 'Administrador'): ?>
      <article class="tl-card tl-system-card">
        <span class="tl-system-icon">🛡️</span>
        <h3>Usuários</h3>
        <p>Gerencie dados cadastrais, status de acesso e perfis dos usuários do sistema.</p>
        <div class="tl-actions"><a class="tl-btn tl-btn-primary" href="<?= tl_url('views/admin/usuarios.php') ?>">Gerenciar</a></div>
      </article>
    <?php endif; ?>
  </section>
</main>
</div>
<script src="<?= tl_url('assets/js/techlounged.js') ?>"></script>
</body>
</html>
