<?php
/**
 * ==========================================================
 * TECHLOUNGED - AVATAR DE BOAS-VINDAS
 * Arquivo: /includes/avatar_boasvindas.php
 *
 * Objetivo:
 * - Exibir uma saudação contextual para visitante ou usuário logado.
 * - Reconhecer corretamente a função do usuário mesmo quando a sessão
 *   vier como funcao, perfil, tipo ou id_funcao.
 * - Evitar conflito de função já declarada quando o include for chamado
 *   mais de uma vez.
 * ==========================================================
 */

require_once(__DIR__ . '/../config/app.php');

if (!defined('AVATAR_WELCOME_INCLUDED')) {
    define('AVATAR_WELCOME_INCLUDED', true);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!function_exists('tl_url')) {
        function tl_url($caminho)
        {
            return BASE_URL . '/' . ltrim($caminho, '/');
        }
    }

    if (!function_exists('tl_avatar_escape')) {
        function tl_avatar_escape($valor)
        {
            return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('tl_avatar_normalizar_texto')) {
        function tl_avatar_normalizar_texto($valor)
        {
            $valor = trim((string) $valor);
            $valor = mb_strtolower($valor, 'UTF-8');

            return str_replace(
                ['á', 'à', 'ã', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'í', 'ì', 'î', 'ï', 'ó', 'ò', 'õ', 'ô', 'ö', 'ú', 'ù', 'û', 'ü', 'ç'],
                ['a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'c'],
                $valor
            );
        }
    }

    if (!function_exists('tl_avatar_obter_usuario_sessao')) {
        function tl_avatar_obter_usuario_sessao()
        {
            $usuarioSessao = $_SESSION['usuario'] ?? null;

            if (!$usuarioSessao) {
                return [
                    'logado' => false,
                    'nome' => 'Visitante',
                    'sobrenome' => '',
                    'funcao_bruta' => 'Visitante',
                    'id_funcao' => null,
                ];
            }

            if (is_array($usuarioSessao)) {
                return [
                    'logado' => true,
                    'nome' => $usuarioSessao['nome']
                        ?? $usuarioSessao['usuario']
                        ?? $usuarioSessao['email']
                        ?? 'Usuário',
                    'sobrenome' => $usuarioSessao['sobrenome'] ?? '',
                    'funcao_bruta' => $usuarioSessao['funcao']
                        ?? $usuarioSessao['nome_funcao']
                        ?? $usuarioSessao['perfil']
                        ?? $usuarioSessao['tipo']
                        ?? $usuarioSessao['id_funcao']
                        ?? 'Usuário',
                    'id_funcao' => $usuarioSessao['id_funcao'] ?? null,
                ];
            }

            return [
                'logado' => true,
                'nome' => (string) $usuarioSessao,
                'sobrenome' => '',
                'funcao_bruta' => 'Usuário',
                'id_funcao' => null,
            ];
        }
    }

    if (!function_exists('tl_avatar_resolver_funcao')) {
        function tl_avatar_resolver_funcao($funcaoBruta, $idFuncao = null)
        {
            $valor = tl_avatar_normalizar_texto($funcaoBruta);
            $id = (string) ($idFuncao ?? '');

            /*
             * Ajuste este mapa caso a sua tabela funcao use IDs diferentes.
             * O objetivo é impedir que o avatar mostre "Usuário" quando a
             * sessão vier apenas com id_funcao.
             */
            $mapaIds = [
                '1' => 'Administrador',
                '2' => 'Bibliotecário',
                '3' => 'Usuário',
            ];

            if ($id !== '' && isset($mapaIds[$id])) {
                return $mapaIds[$id];
            }

            if (isset($mapaIds[(string) $funcaoBruta])) {
                return $mapaIds[(string) $funcaoBruta];
            }

            if (str_contains($valor, 'admin') || str_contains($valor, 'administrador')) {
                return 'Administrador';
            }

            if (str_contains($valor, 'bibliotec')) {
                return 'Bibliotecário';
            }

            if (
                str_contains($valor, 'aluno') ||
                str_contains($valor, 'estudante') ||
                str_contains($valor, 'discente')
            ) {
                return 'Aluno';
            }

            if (
                str_contains($valor, 'usuario') ||
                str_contains($valor, 'comum') ||
                str_contains($valor, 'user')
            ) {
                return 'Usuário';
            }

            if ($valor === 'visitante' || $valor === '') {
                return 'Visitante';
            }

            return trim((string) $funcaoBruta) ?: 'Usuário';
        }
    }

    if (!function_exists('tl_avatar_mensagem_por_funcao')) {
        function tl_avatar_mensagem_por_funcao($funcao)
        {
            $funcaoNormalizada = tl_avatar_normalizar_texto($funcao);

            if ($funcaoNormalizada === 'administrador') {
                return 'Você possui acesso administrativo completo à plataforma TechLounged.';
            }

            if ($funcaoNormalizada === 'bibliotecario') {
                return 'Gerencie eventos, espaços, atividades, cursos, usuários e recursos da biblioteca.';
            }

            if ($funcaoNormalizada === 'aluno') {
                return 'Explore oficinas, eventos, conteúdos educacionais e recursos disponíveis.';
            }

            if ($funcaoNormalizada === 'usuario') {
                return 'Acompanhe seus eventos, inscrições e dados do perfil.';
            }

            return 'Faça login para acessar recursos exclusivos da plataforma TechLounged.';
        }
    }

    if (!function_exists('tl_avatar_obter_iniciais')) {
        function tl_avatar_obter_iniciais($nome, $sobrenome = '')
        {
            $nome = trim((string) $nome);
            $sobrenome = trim((string) $sobrenome);

            $primeira = $nome !== '' ? mb_substr($nome, 0, 1, 'UTF-8') : '';
            $segunda = $sobrenome !== '' ? mb_substr($sobrenome, 0, 1, 'UTF-8') : '';

            if ($segunda === '' && str_contains($nome, ' ')) {
                $partes = preg_split('/\s+/', $nome);
                $ultimaParte = end($partes);
                $segunda = $ultimaParte ? mb_substr($ultimaParte, 0, 1, 'UTF-8') : '';
            }

            $iniciais = mb_strtoupper($primeira . $segunda, 'UTF-8');

            return $iniciais ?: 'TL';
        }
    }

    $usuario = tl_avatar_obter_usuario_sessao();
    $usuarioNome = $usuario['nome'];
    $usuarioSobrenome = $usuario['sobrenome'];
    $usuarioTipo = tl_avatar_resolver_funcao($usuario['funcao_bruta'], $usuario['id_funcao']);
    $mensagemExtra = tl_avatar_mensagem_por_funcao($usuarioTipo);
    $iniciais = tl_avatar_obter_iniciais($usuarioNome, $usuarioSobrenome);
    $lottieBotUrl = tl_url('imagem/bot-ola.json');
?>

<div id="tl-avatar-container" aria-live="polite">
    <div id="tl-chatbox" role="dialog" aria-label="Mensagem de boas-vindas do TechLounged" aria-hidden="true">
        <button id="tl-close-avatar" type="button" aria-label="Fechar mensagem do assistente">
            ×
        </button>

        <div class="tl-avatar-header">
            <div class="tl-avatar-lottie" aria-hidden="true">
                <lottie-player
                    src="<?= tl_avatar_escape($lottieBotUrl) ?>"
                    background="transparent"
                    speed="1.5"
                    loop
                    autoplay>
                </lottie-player>
            </div>

            <div>
                <h3>Assistente TechLounged</h3>
                <span>Online agora</span>
            </div>
        </div>

        <div class="tl-message">
            <p>
                Olá,
                <strong><?= tl_avatar_escape($usuarioNome); ?></strong> 👋
            </p>

            <p>
                <strong>Perfil:</strong>
                <?= tl_avatar_escape($usuarioTipo); ?>
            </p>

            <p>
                <?= tl_avatar_escape($mensagemExtra); ?>
            </p>
        </div>
    </div>

    <button id="tl-avatar-button" type="button" aria-label="Abrir mensagem de boas-vindas">
        <span id="tl-avatar-notification">1</span>

        <span class="tl-avatar-button-animation" aria-hidden="true">
            <lottie-player
                src="<?= tl_avatar_escape($lottieBotUrl) ?>"
                background="transparent"
                speed="1.5"
                loop
                autoplay>
            </lottie-player>
        </span>

        <span class="tl-avatar-fallback">
            <?= tl_avatar_escape($iniciais); ?>
        </span>
    </button>
</div>

<script>
(function carregarLottieAvatar() {
    if (window.customElements && customElements.get('lottie-player')) {
        return;
    }

    if (document.getElementById('tl-lottie-player-script')) {
        return;
    }

    const script = document.createElement('script');
    script.id = 'tl-lottie-player-script';
    script.src = 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js';
    script.defer = true;
    document.head.appendChild(script);
})();
</script>

<style>
#tl-avatar-container {
    --tl-avatar-primary: #004B87;
    --tl-avatar-secondary: #0072CE;
    --tl-avatar-soft: #E2EBF4;
    --tl-avatar-slate: #708090;
    --tl-avatar-bg: #ffffff;
    --tl-avatar-text: #263238;
    --tl-avatar-muted: #5f6f7a;
    --tl-avatar-shadow: 0 18px 45px rgba(0, 0, 0, .20);

    position: fixed;
    right: 25px;
    bottom: 25px;
    z-index: 999999;
    font-family: Arial, Helvetica, sans-serif;
}

[data-theme="dark"] #tl-avatar-container,
body.dark #tl-avatar-container,
body.tl-dark #tl-avatar-container {
    --tl-avatar-bg: #0f172a;
    --tl-avatar-text: #e5edf5;
    --tl-avatar-muted: #b8c5d1;
    --tl-avatar-soft: #172033;
    --tl-avatar-shadow: 0 18px 45px rgba(0, 0, 0, .42);
}

#tl-avatar-button {
    width: 86px;
    height: 86px;
    border: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--tl-avatar-primary), var(--tl-avatar-secondary));
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    box-shadow: var(--tl-avatar-shadow);
    transition: transform .25s ease, box-shadow .25s ease, opacity .25s ease;
    animation: tlAvatarFloat 3s ease-in-out infinite;
    position: relative;
    overflow: visible;
}

#tl-avatar-button:hover {
    transform: scale(1.06);
}

#tl-avatar-notification {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #ef4444;
    color: #fff;
    font-size: 12px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 700;
    animation: tlAvatarPulse 1.5s infinite;
}

.tl-avatar-button-animation,
.tl-avatar-lottie {
    display: flex;
    align-items: center;
    justify-content: center;
}

.tl-avatar-button-animation lottie-player {
    width: 112px;
    height: 112px;
}

.tl-avatar-lottie {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .16);
    flex: 0 0 auto;
    overflow: hidden;
}

.tl-avatar-lottie lottie-player {
    width: 92px;
    height: 92px;
}

.tl-avatar-fallback {
    display: none;
    color: #fff;
    font-size: 24px;
    font-weight: 800;
    letter-spacing: .5px;
}

#tl-chatbox {
    width: min(340px, calc(100vw - 36px));
    background: var(--tl-avatar-bg);
    border: 1px solid rgba(112, 128, 144, .25);
    border-radius: 18px;
    box-shadow: var(--tl-avatar-shadow);
    position: absolute;
    bottom: 108px;
    right: 0;
    overflow: hidden;
    display: none;
    animation: tlAvatarFade .35s ease;
}

.tl-avatar-header {
    background: linear-gradient(135deg, var(--tl-avatar-primary), var(--tl-avatar-secondary));
    color: #fff;
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 14px;
}

.tl-avatar-header h3 {
    margin: 0;
    font-size: 17px;
    line-height: 1.2;
}

.tl-avatar-header span {
    font-size: 12px;
    opacity: .88;
}

.tl-message {
    padding: 22px;
}

.tl-message p {
    margin: 0 0 12px;
    line-height: 1.6;
    color: var(--tl-avatar-text);
    font-size: 14px;
}

.tl-message p:last-child {
    margin-bottom: 0;
    color: var(--tl-avatar-muted);
}

#tl-close-avatar {
    position: absolute;
    top: 9px;
    right: 12px;
    width: 28px;
    height: 28px;
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, .14);
    color: #fff;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
    z-index: 2;
    transition: transform .2s ease, background .2s ease;
}

#tl-close-avatar:hover {
    transform: scale(1.08);
    background: rgba(255, 255, 255, .24);
}

#tl-avatar-container.tl-hidden {
    opacity: 0 !important;
    visibility: hidden !important;
    transform: translateY(20px) scale(.92);
    transition: all .5s ease;
}

@keyframes tlAvatarFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

@keyframes tlAvatarFade {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes tlAvatarPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}

@media (max-width: 768px) {
    #tl-avatar-container {
        right: 15px;
        bottom: 15px;
    }

    #tl-avatar-button {
        width: 72px;
        height: 72px;
    }

    .tl-avatar-button-animation lottie-player {
        width: 96px;
        height: 96px;
    }

    #tl-chatbox {
        bottom: 92px;
        right: 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    #tl-avatar-button,
    #tl-avatar-notification,
    #tl-chatbox {
        animation: none;
    }

    #tl-avatar-button,
    #tl-close-avatar,
    #tl-avatar-container {
        transition: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const avatarContainer = document.getElementById('tl-avatar-container');
    const avatarButton = document.getElementById('tl-avatar-button');
    const chatbox = document.getElementById('tl-chatbox');
    const closeButton = document.getElementById('tl-close-avatar');
    const notification = document.getElementById('tl-avatar-notification');

    if (!avatarContainer || !avatarButton || !chatbox || !closeButton) {
        return;
    }

    const TEMPO_ABRIR = 1500;
    const TEMPO_FECHAR = 15000;
    const TEMPO_SUMIR = 20000;
    const chaveData = 'tl_avatar_data';
    const chaveInteragiu = 'tl_avatar_interagiu';
    const chaveOcultado = 'tl_avatar_ocultado';
    const hoje = new Date().toISOString().slice(0, 10);

    function abrirChatbox() {
        chatbox.style.display = 'block';
        chatbox.setAttribute('aria-hidden', 'false');

        if (notification) {
            notification.style.display = 'none';
        }
    }

    function fecharChatbox() {
        chatbox.style.display = 'none';
        chatbox.setAttribute('aria-hidden', 'true');
    }

    function ocultarAvatar() {
        avatarContainer.classList.add('tl-hidden');
        localStorage.setItem(chaveOcultado, 'sim');

        setTimeout(function () {
            avatarContainer.style.display = 'none';
        }, 500);
    }

    function marcarInteracao() {
        localStorage.setItem(chaveInteragiu, 'sim');
    }

    if (localStorage.getItem(chaveData) !== hoje) {
        localStorage.removeItem(chaveInteragiu);
        localStorage.removeItem(chaveOcultado);
        localStorage.setItem(chaveData, hoje);
    }

    if (
        localStorage.getItem(chaveInteragiu) === 'sim' ||
        localStorage.getItem(chaveOcultado) === 'sim'
    ) {
        avatarContainer.style.display = 'none';
        return;
    }

    const timerAbrir = setTimeout(abrirChatbox, TEMPO_ABRIR);
    const timerFechar = setTimeout(fecharChatbox, TEMPO_FECHAR);
    const timerOcultar = setTimeout(ocultarAvatar, TEMPO_SUMIR);

    avatarButton.addEventListener('click', function () {
        marcarInteracao();

        clearTimeout(timerAbrir);
        clearTimeout(timerFechar);
        clearTimeout(timerOcultar);

        if (chatbox.style.display === 'block') {
            fecharChatbox();
        } else {
            abrirChatbox();
        }
    });

    closeButton.addEventListener('click', function () {
        marcarInteracao();
        fecharChatbox();
    });

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape' && chatbox.style.display === 'block') {
            marcarInteracao();
            fecharChatbox();
        }
    });
});
</script>

<?php } ?>
