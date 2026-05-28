<?php
require_once(__DIR__ . '/../../middleware/auth.php');
require_once(__DIR__ . '/../../config/app.php');
require_once(__DIR__ . '/../../config/conexao.php');

$funcaoUsuario = $_SESSION['usuario']['funcao'] ?? $_SESSION['usuario']['nome_funcao'] ?? '';
$idFuncao = $_SESSION['usuario']['id_funcao'] ?? null;
$ehAdmin = in_array($funcaoUsuario, ['Administrador', 'Bibliotecário', 'Bibliotecario']) || in_array((string)$idFuncao, ['1', '2']);

if (!$ehAdmin) {
    echo '<script>alert("Acesso restrito à administração."); window.location.href="' . BASE_URL . '/views/eventos.php";</script>';
    exit;
}

$idRegistro = intval($_GET['id'] ?? 0);

if ($idRegistro <= 0) {
    echo '<script>alert("Evento inválido."); window.location.href="' . BASE_URL . '/views/eventos.php";</script>';
    exit;
}

$sql = "
    SELECT
        ra.id,
        ra.data_execucao,
        ra.data_finalizacao,
        ra.tema_especifico,
        ra.status,
        ra.publico_previsto,
        ra.url_imagem,
        a.nome_projeto,
        a.objetivo,
        a.eh_publico,
        a.url_imagem AS url_imagem_atividade,
        COALESCE(NULLIF(ra.url_imagem, ''), NULLIF(a.url_imagem, '')) AS imagem_exibicao,
        e.nome_espaco,
        e.capacidade_maxima
    FROM registro_atividade ra
    INNER JOIN atividade a ON a.id = ra.id_atividade
    INNER JOIN espaco e ON e.id = ra.id_espaco
    WHERE ra.id = ?
    LIMIT 1
";

$stmt = $conexao->prepare($sql);
$stmt->bind_param('i', $idRegistro);
$stmt->execute();
$evento = $stmt->get_result()->fetch_assoc();

if (!$evento) {
    echo '<script>alert("Evento não encontrado."); window.location.href="' . BASE_URL . '/views/eventos.php";</script>';
    exit;
}

if (intval($evento['eh_publico']) !== 1) {
    echo '<script>alert("O panfleto com QR Code só está disponível para atividades públicas."); window.location.href="' . BASE_URL . '/views/eventos.php";</script>';
    exit;
}

$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
$urlEvento = $protocolo . '://' . $host . BASE_URL . '/views/eventos.php?id_evento=' . $idRegistro;
$urlQr = 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=' . urlencode($urlEvento);
$imagem = $evento['imagem_exibicao'] ?: 'https://placehold.co/900x420/E2EBF4/004B87?text=TechLounged';

function h($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function dataBR($data) {
    if (!$data) return 'Data a definir';
    return date('d/m/Y', strtotime($data));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panfleto - <?= h($evento['nome_projeto']) ?></title>
  <style>
    :root {
      --azul-institucional: #004B87;
      --azul-dinamico: #0072CE;
      --azul-claro: #E2EBF4;
      --cinza-slate: #708090;
      --cinza-fundo: #F4F5F7;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      background: var(--cinza-fundo);
      color: #17202a;
      font-family: Arial, Helvetica, sans-serif;
    }
    .barra-acoes {
      max-width: 900px;
      margin: 18px auto;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }
    .btn {
      border: 0;
      border-radius: 999px;
      padding: 10px 18px;
      color: #fff;
      background: var(--azul-dinamico);
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
    }
    .btn-secundario { background: var(--cinza-slate); }
    .panfleto {
      width: 900px;
      min-height: 1180px;
      margin: 0 auto 28px;
      background: #fff;
      border-radius: 28px;
      overflow: hidden;
      box-shadow: 0 24px 70px rgba(0, 75, 135, .16);
      border: 1px solid rgba(0, 75, 135, .12);
    }
    .hero {
      min-height: 420px;
      padding: 42px;
      color: #fff;
      background:
        linear-gradient(120deg, rgba(0,75,135,.95), rgba(0,114,206,.78)),
        url('<?= h($imagem) ?>') center/cover no-repeat;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .marca {
      display: flex;
      align-items: center;
      gap: 14px;
      font-weight: 800;
      letter-spacing: .3px;
    }
    .marca span:first-child {
      width: 54px;
      height: 54px;
      border-radius: 18px;
      display: grid;
      place-items: center;
      background: #fff;
      color: var(--azul-institucional);
      font-size: 20px;
    }
    .kicker {
      display: inline-flex;
      width: max-content;
      padding: 8px 14px;
      border-radius: 999px;
      background: rgba(255,255,255,.2);
      border: 1px solid rgba(255,255,255,.35);
      font-weight: 700;
      text-transform: uppercase;
      font-size: 12px;
      letter-spacing: .08em;
    }
    h1 {
      margin: 18px 0 12px;
      font-size: 54px;
      line-height: 1.02;
      max-width: 760px;
    }
    .subtitulo {
      max-width: 720px;
      font-size: 21px;
      line-height: 1.45;
      margin: 0;
    }
    .conteudo {
      padding: 42px;
      display: grid;
      grid-template-columns: 1fr 270px;
      gap: 32px;
      align-items: start;
    }
    .info-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
      margin-top: 24px;
    }
    .info {
      background: var(--cinza-fundo);
      border: 1px solid var(--azul-claro);
      border-radius: 18px;
      padding: 18px;
    }
    .info small {
      display: block;
      color: var(--cinza-slate);
      font-weight: 700;
      text-transform: uppercase;
      font-size: 11px;
      letter-spacing: .06em;
      margin-bottom: 8px;
    }
    .info strong { color: var(--azul-institucional); font-size: 18px; }
    .qr-card {
      border: 4px solid var(--azul-claro);
      border-radius: 24px;
      padding: 18px;
      text-align: center;
      background: #fff;
    }
    .qr-card img {
      width: 100%;
      height: auto;
      display: block;
    }
    .qr-card strong {
      display: block;
      color: var(--azul-institucional);
      font-size: 20px;
      margin-top: 12px;
    }
    .qr-card p {
      color: var(--cinza-slate);
      font-size: 14px;
      line-height: 1.35;
      margin: 8px 0 0;
    }
    .url {
      word-break: break-all;
      font-size: 11px !important;
    }
    .rodape {
      background: var(--azul-claro);
      padding: 24px 42px;
      color: var(--azul-institucional);
      font-weight: 700;
      display: flex;
      justify-content: space-between;
      gap: 22px;
    }
    @media print {
      @page { size: A4; margin: 8mm; }
      body { background: #fff; }
      .barra-acoes { display: none; }
      .panfleto {
        width: 100%;
        min-height: auto;
        margin: 0;
        border-radius: 0;
        box-shadow: none;
        border: 0;
      }
      .hero { min-height: 360px; }
      h1 { font-size: 44px; }
      .conteudo { grid-template-columns: 1fr 230px; }
    }
  </style>
</head>
<body>
  <div class="barra-acoes">
    <a class="btn btn-secundario" href="<?= BASE_URL ?>/views/eventos.php">Voltar aos eventos</a>
    <button class="btn" onclick="window.print()">Gerar PDF / Imprimir</button>
  </div>

  <article class="panfleto">
    <section class="hero">
      <div class="marca"><span>TL</span><span>TechLounged<br><small>Biblioteca • Eventos • Inscrições</small></span></div>
      <div>
        <span class="kicker">Evento público da biblioteca</span>
        <h1><?= h($evento['nome_projeto']) ?></h1>
        <p class="subtitulo"><?= h($evento['tema_especifico'] ?: $evento['objetivo']) ?></p>
      </div>
    </section>

    <section class="conteudo">
      <div>
        <h2 style="color:var(--azul-institucional); font-size:30px; margin:0 0 12px;">Participe deste evento</h2>
        <p style="color:#46525f; font-size:17px; line-height:1.6; margin:0;">
          Escaneie o QR Code para abrir a página do evento, consultar as informações atualizadas e realizar sua inscrição.
        </p>

        <div class="info-grid">
          <div class="info"><small>Data</small><strong><?= dataBR($evento['data_execucao']) ?><?= $evento['data_finalizacao'] ? ' até ' . dataBR($evento['data_finalizacao']) : '' ?></strong></div>
          <div class="info"><small>Local</small><strong><?= h($evento['nome_espaco']) ?></strong></div>
          <div class="info"><small>Vagas</small><strong><?= h($evento['publico_previsto'] ?: $evento['capacidade_maxima'] ?: 'A definir') ?></strong></div>
          <div class="info"><small>Status</small><strong><?= h($evento['status']) ?></strong></div>
        </div>
      </div>

      <aside class="qr-card">
        <img src="<?= h($urlQr) ?>" alt="QR Code para inscrição no evento">
        <strong>Inscreva-se</strong>
        <p>Aponte a câmera do celular para acessar o evento.</p>
        <p class="url"><?= h($urlEvento) ?></p>
      </aside>
    </section>

    <footer class="rodape">
      <span>TechLounged - Sistema de eventos da biblioteca</span>
      <span>Evento #<?= h($evento['id']) ?></span>
    </footer>
  </article>

  <script>
    window.addEventListener('load', () => {
      setTimeout(() => window.print(), 650);
    });
  </script>
</body>
</html>
