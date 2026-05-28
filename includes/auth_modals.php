<?php
require_once(__DIR__ . '/../config/app.php');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!function_exists('tl_url')) {
    function tl_url($caminho) {
        return BASE_URL . '/' . ltrim($caminho, '/');
    }
}

if (!empty($_SESSION['usuario'])) {
    return;
}
?>
<div class="tl-modal-overlay tl-auth-overlay" id="tlAuthOverlay" onclick="tlFecharModais()"></div>

<div class="tl-modal tl-auth-modal" id="modalLogin" role="dialog" aria-modal="true" aria-labelledby="tituloModalLogin">
  <button class="tl-modal-close" type="button" onclick="tlFecharModais()" aria-label="Fechar">×</button>
  <div class="tl-auth-head">
    <span class="tl-brand-mark">TL</span>
    <div>
      <h3 id="tituloModalLogin">Entrar no TechLounged</h3>
      <p>Acesse sua conta para se inscrever nos eventos da biblioteca.</p>
    </div>
  </div>

  <form action="<?= tl_url('services/auth/login.php') ?>" method="POST" class="tl-grid tl-auth-form">
    <input type="hidden" name="origem" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? tl_url('views/eventos.php')) ?>">

    <div class="tl-field">
      <label for="login_email">E-mail</label>
      <input type="email" id="login_email" name="email" required autocomplete="email" placeholder="seu.email@exemplo.com">
    </div>

    <div class="tl-field">
      <label for="login_senha">Senha</label>
      <input type="password" id="login_senha" name="senha" required autocomplete="current-password" placeholder="Digite sua senha">
    </div>

    <button class="tl-btn tl-btn-primary tl-full" type="submit">Entrar</button>

    <p class="tl-auth-switch">
      Ainda não possui conta?
      <button type="button" onclick="tlAbrirCadastro()">Criar cadastro</button>
    </p>
  </form>
</div>

<div class="tl-modal tl-auth-modal" id="modalCadastro" role="dialog" aria-modal="true" aria-labelledby="tituloModalCadastro">
  <button class="tl-modal-close" type="button" onclick="tlFecharModais()" aria-label="Fechar">×</button>
  <div class="tl-auth-head">
    <span class="tl-brand-mark">TL</span>
    <div>
      <h3 id="tituloModalCadastro">Criar conta</h3>
      <p>Cadastre-se para acompanhar sua participação nas atividades da biblioteca.</p>
    </div>
  </div>

  <form action="<?= tl_url('services/auth/cadastrar.php') ?>" method="POST" class="tl-form-grid tl-auth-form">
    <input type="hidden" name="origem" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? tl_url('views/eventos.php')) ?>">

    <div class="tl-field">
      <label for="cadastro_nome">Nome</label>
      <input id="cadastro_nome" name="nome" required autocomplete="given-name" placeholder="Seu nome">
    </div>

    <div class="tl-field">
      <label for="cadastro_sobrenome">Sobrenome</label>
      <input id="cadastro_sobrenome" name="sobrenome" autocomplete="family-name" placeholder="Seu sobrenome">
    </div>

    <div class="tl-field tl-full">
      <label for="cadastro_email">E-mail</label>
      <input type="email" id="cadastro_email" name="email" required autocomplete="email" placeholder="seu.email@exemplo.com">
    </div>

    <div class="tl-field">
      <label for="cadastro_matricula">Matrícula</label>
      <input id="cadastro_matricula" name="matricula" placeholder="Opcional">
    </div>

    <div class="tl-field">
      <label for="cadastro_sexo">Sexo</label>
      <select id="cadastro_sexo" name="sexo">
        <option value="Prefiro não informar">Prefiro não informar</option>
        <option value="Feminino">Feminino</option>
        <option value="Masculino">Masculino</option>
        <option value="Outro">Outro</option>
      </select>
    </div>

    <div class="tl-field tl-full">
      <label for="cadastro_senha">Senha</label>
      <input type="password" id="cadastro_senha" name="senha" required autocomplete="new-password" placeholder="Crie uma senha">
    </div>

    <button class="tl-btn tl-btn-primary tl-full" type="submit">Cadastrar</button>

    <p class="tl-auth-switch tl-full">
      Já possui conta?
      <button type="button" onclick="tlAbrirLogin()">Entrar agora</button>
    </p>
  </form>
</div>
