<?php
require_once(__DIR__ . '/../middleware/auth.php');
require_once(__DIR__ . '/../config/app.php');
$u = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Meu perfil | TechLounged</title><link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/techlounged.css"></head>
<body>
<?php include(__DIR__ . '/../includes/topo.php'); ?>
<main class="tl-section">
  <div class="tl-container tl-grid tl-grid-2">
    <section class="tl-card tl-card-pad">
      <span class="tl-kicker">Área do usuário</span>
      <h1 style="color:var(--azul-institucional); margin:16px 0 8px;">Meu perfil</h1>
      <p class="tl-meta">Confira seus dados cadastrais e mantenha suas informações atualizadas.</p>
      <div class="tl-stat-grid">
        <div class="tl-stat"><strong><?= htmlspecialchars($u['nome'] ?? 'Usuário') ?></strong><span>Nome</span></div>
        <div class="tl-stat"><strong><?= htmlspecialchars($u['funcao'] ?? $u['id_funcao'] ?? 'Visitante') ?></strong><span>Função</span></div>
        <div class="tl-stat"><strong><?= htmlspecialchars($u['matricula'] ?? '-') ?></strong><span>Matrícula</span></div>
      </div>
    </section>

    <section class="tl-card tl-card-pad">
      <h2 style="color:var(--azul-institucional); margin-top:0;">Dados pessoais</h2>
      <form class="tl-form-grid" action="<?= tl_url('services/auth/atualizar_perfil.php') ?>" method="POST">
        <div class="tl-field"><label>Nome</label><input name="nome" value="<?= htmlspecialchars($u['nome'] ?? '') ?>" required></div>
        <div class="tl-field"><label>Sobrenome</label><input name="sobrenome" value="<?= htmlspecialchars($u['sobrenome'] ?? '') ?>"></div>
        <div class="tl-field tl-full"><label>E-mail</label><input type="email" name="email" value="<?= htmlspecialchars($u['email'] ?? '') ?>" required></div>
        <div class="tl-field"><label>Matrícula</label><input name="matricula" value="<?= htmlspecialchars($u['matricula'] ?? '') ?>"></div>
        <div class="tl-field"><label>Sexo</label><select name="sexo">
          <option value="Prefiro não informar" <?= (($u['sexo'] ?? '') === 'Prefiro não informar') ? 'selected' : '' ?>>Prefiro não informar</option>
          <option value="Masculino" <?= (($u['sexo'] ?? '') === 'Masculino') ? 'selected' : '' ?>>Masculino</option>
          <option value="Feminino" <?= (($u['sexo'] ?? '') === 'Feminino') ? 'selected' : '' ?>>Feminino</option>
        </select></div>
        <div class="tl-actions tl-full"><button class="tl-btn tl-btn-primary" type="submit">Salvar alterações</button><a class="tl-btn tl-btn-secondary" href="<?= tl_url('views/minhas_inscricoes.php') ?>">Ver minhas inscrições</a></div>
      </form>
      
    </section>
  </div>
</main>
<script src="<?= tl_url('assets/js/techlounged.js') ?>"></script>
</body>
</html>
