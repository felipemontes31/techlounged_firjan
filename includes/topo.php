<?php
require_once(__DIR__ . '/../config/app.php');
if (session_status() === PHP_SESSION_NONE) session_start();

$usuarioLogado = $_SESSION['usuario'] ?? null;
$nomeUsuario = $usuarioLogado['nome'] ?? 'Visitante';
$sobrenomeUsuario = $usuarioLogado['sobrenome'] ?? '';

$funcaoBruta = $usuarioLogado['funcao'] 
    ?? $usuarioLogado['id_funcao'] 
    ?? 'Visitante';

$funcaoUsuario = normalizarFuncaoUsuario($funcaoBruta);

$iniciais = mb_strtoupper(
    mb_substr($nomeUsuario, 0, 1) .
    mb_substr($sobrenomeUsuario, 0, 1),
    "UTF-8"
);

if (trim($iniciais) === "") {
    $iniciais = "TL";
}

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

function normalizarFuncaoUsuario($funcao)
{
    $funcao = trim((string) $funcao);

    $mapa = [
        "1" => "Administrador",
        "2" => "Bibliotecário",
        "3" => "Usuário",

        "admin" => "Administrador",
        "administrador" => "Administrador",

        "bibliotecario" => "Bibliotecário",
        "bibliotecário" => "Bibliotecário",

        "usuario" => "Usuário",
        "usuário" => "Usuário",
        "comum" => "Usuário"
    ];

    $chave = mb_strtolower($funcao, "UTF-8");
    $chave = str_replace(
        ["á", "à", "ã", "â", "é", "ê", "í", "ó", "ô", "õ", "ú", "ç"],
        ["a", "a", "a", "a", "e", "e", "i", "o", "o", "o", "u", "c"],
        $chave
    );

    return $mapa[$chave] ?? $funcao;
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
        <a href="<?= tl_url('views/login.php') ?>" onclick="event.preventDefault(); tlAbrirLogin();">Entrar</a>
        <a class="ativo" href="<?= tl_url('views/cadastro.php') ?>" onclick="event.preventDefault(); tlAbrirCadastro();">Cadastrar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<?php include(__DIR__ . '/auth_modals.php'); ?>
