<?php
/**
 * ==========================================================
 * TECHLOUNGED - AVATAR INTELIGENTE
 * Arquivo:
 * /includes/avatar_boasvindas.php
 * ==========================================================
 */

if (!defined('AVATAR_WELCOME_INCLUDED')) {

    define('AVATAR_WELCOME_INCLUDED', true);

    // ======================================================
    // INICIA SESSÃO
    // ======================================================

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // ======================================================
    // CONFIGURAÇÕES PADRÃO
    // ======================================================

    $usuarioNome = 'Visitante';

    $usuarioTipo = 'Visitante';

    $mensagemExtra = '
        Faça login para acessar recursos exclusivos
        da plataforma TechLounged.
    ';

    // ======================================================
    // VERIFICA SE EXISTE USUÁRIO LOGADO
    // ======================================================

    if (isset($_SESSION['usuario'])) {

        // ==================================================
        // CASO A SESSÃO SEJA ARRAY
        // ==================================================

        if (is_array($_SESSION['usuario'])) {

            $usuario = $_SESSION['usuario'];

            // NOME

            $usuarioNome = htmlspecialchars(
                $usuario['nome']
                ?? $usuario['usuario']
                ?? 'Usuário'
            );

            // PERFIL

            $perfil = strtolower(
                trim(
                    $usuario['perfil']
                    ?? $usuario['tipo']
                    ?? ''
                )
            );

        }

        // ==================================================
        // CASO A SESSÃO SEJA STRING
        // ==================================================

        else {

            $usuarioNome = htmlspecialchars($_SESSION['usuario']);

            $perfil = strtolower($usuarioNome);

        }

        // ==================================================
        // DETECÇÃO AUTOMÁTICA DE PERFIL
        // ==================================================

        if (
            strpos($perfil, 'admin') !== false
            || strpos($perfil, 'administrador') !== false
        ) {

            $usuarioTipo = 'Administrador';

            $mensagemExtra = '
                Você possui acesso administrativo completo
                da plataforma TechLounged.
            ';

        }

        else if (
            strpos($perfil, 'bibliotec') !== false
        ) {

            $usuarioTipo = 'Bibliotecária';

            $mensagemExtra = '
                Gerencie eventos, espaços, atividades
                e recursos da biblioteca.
            ';

        }

        else if (
            strpos($perfil, 'aluno') !== false
            || strpos($perfil, 'estudante') !== false
        ) {

            $usuarioTipo = 'Aluno';

            $mensagemExtra = '
                Explore oficinas, eventos,
                conteúdos educacionais e recursos disponíveis.
            ';

        }

        else {

            $usuarioTipo = 'Usuário';

            $mensagemExtra = '
                Bem-vindo novamente ao TechLounged.
            ';

        }
    }

?>

<!-- ==========================================================
AVATAR
========================================================== -->

<div id="tl-avatar-container">

    <!-- ======================================================
    CHATBOX
    ======================================================= -->

    <div id="tl-chatbox">

        <div id="tl-close-avatar">
            ✖
        </div>

        <!-- HEADER -->

        <div class="tl-avatar-header">

            <div class="tl-avatar-icon">
                🤖
            </div>

            <div>

                <h3>
                    Assistente TechLounged
                </h3>

                <span>
                    Online agora
                </span>

            </div>

        </div>

        <!-- MENSAGEM -->

        <div class="tl-message">

            <p>
                Olá,
                <strong><?= $usuarioNome; ?></strong> 👋
            </p>

            <p>
                <strong>Perfil:</strong>
                <?= $usuarioTipo; ?>
            </p>

            <p>
                <?= $mensagemExtra; ?>
            </p>

        </div>

    </div>

    <!-- ======================================================
    BOTÃO AVATAR
    ======================================================= -->

    <div id="tl-avatar-button">

        <div id="tl-avatar-notification">
            1
        </div>

        <div class="tl-avatar-face">
            🤖
        </div>

    </div>

</div>

<!-- ==========================================================
CSS
========================================================== -->

<style>

#tl-avatar-container{

    position:fixed;
    right:25px;
    bottom:25px;
    z-index:999999;
    font-family:Arial, Helvetica, sans-serif;

}

/* ==========================================================
BOTÃO AVATAR
========================================================== */

#tl-avatar-button{

    width:85px;
    height:85px;
    border-radius:50%;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    display:flex;
    justify-content:center;
    align-items:center;
    cursor:pointer;
    box-shadow:0 15px 35px rgba(0,0,0,0.25);
    transition:all .3s ease;
    animation:tlFloat 3s ease-in-out infinite;
    position:relative;

}

#tl-avatar-button:hover{

    transform:scale(1.08);

}

.tl-avatar-face{

    font-size:40px;

}

/* ==========================================================
NOTIFICAÇÃO
========================================================== */

#tl-avatar-notification{

    position:absolute;
    top:-2px;
    right:-2px;
    width:24px;
    height:24px;
    border-radius:50%;
    background:#ef4444;
    color:#fff;
    font-size:12px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-weight:bold;
    animation:tlPulse 1.5s infinite;

}

/* ==========================================================
CHATBOX
========================================================== */

#tl-chatbox{

    width:340px;
    background:#ffffff;
    border-radius:18px;
    box-shadow:0 15px 45px rgba(0,0,0,0.18);
    position:absolute;
    bottom:105px;
    right:0;
    overflow:hidden;
    display:none;
    animation:tlFade .4s ease;

}

/* ==========================================================
HEADER
========================================================== */

.tl-avatar-header{

    background:linear-gradient(135deg,#1e3a8a,#2563eb);
    color:#fff;
    padding:18px;
    display:flex;
    align-items:center;
    gap:15px;

}

.tl-avatar-header h3{

    margin:0;
    font-size:17px;

}

.tl-avatar-header span{

    font-size:12px;
    opacity:.85;

}

.tl-avatar-icon{

    width:55px;
    height:55px;
    border-radius:50%;
    background:rgba(255,255,255,.15);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;

}
/* Ocultação suave do avatar */
.tl-hidden{
    opacity: 0 !important;
    visibility: hidden !important;
    transform: translateY(20px) scale(0.8);
    transition: all 0.5s ease;
}

/* ==========================================================
MENSAGEM
========================================================== */

.tl-message{

    padding:22px;

}

.tl-message p{

    margin:0 0 12px;
    line-height:1.6;
    color:#334155;
    font-size:14px;

}

/* ==========================================================
BOTÃO FECHAR
========================================================== */

#tl-close-avatar{

    position:absolute;
    top:10px;
    right:14px;
    color:#fff;
    cursor:pointer;
    font-size:16px;
    z-index:2;
    transition:.2s;

}

#tl-close-avatar:hover{

    transform:scale(1.2);

}

/* ==========================================================
ANIMAÇÕES
========================================================== */

@keyframes tlFloat{

    0%{
        transform:translateY(0px);
    }

    50%{
        transform:translateY(-8px);
    }

    100%{
        transform:translateY(0px);
    }

}

@keyframes tlFade{

    from{

        opacity:0;
        transform:translateY(20px);

    }

    to{

        opacity:1;
        transform:translateY(0);

    }

}

@keyframes tlPulse{

    0%{
        transform:scale(1);
    }

    50%{
        transform:scale(1.15);
    }

    100%{
        transform:scale(1);
    }

}

/* ==========================================================
RESPONSIVO
========================================================== */

@media(max-width:768px){

    #tl-avatar-container{

        right:15px;
        bottom:15px;

    }

    #tl-avatar-button{

        width:72px;
        height:72px;

    }

    .tl-avatar-face{

        font-size:34px;

    }

    #tl-chatbox{

        width:290px;

    }

}

</style>

<!-- ==========================================================
JAVASCRIPT
========================================================== -->
<script>

document.addEventListener('DOMContentLoaded', function(){

    const avatarContainer = document.getElementById('tl-avatar-container');
    const avatarButton = document.getElementById('tl-avatar-button');
    const chatbox = document.getElementById('tl-chatbox');
    const closeButton = document.getElementById('tl-close-avatar');
    const notification = document.getElementById('tl-avatar-notification');

    // Tempo de exibição
    const TEMPO_ABRIR = 1500;   // 1,5 segundos
    const TEMPO_FECHAR = 15000; // 15 segundos
    const TEMPO_SUMIR = 20000;  // 20 segundos

    // Controle diário
    const hoje = new Date().toLocaleDateString();

    if(localStorage.getItem('tl_avatar_data') !== hoje){

        localStorage.removeItem('tl_avatar_interagiu');
        localStorage.removeItem('tl_avatar_ocultado');

        localStorage.setItem('tl_avatar_data', hoje);
    }

    // Se já foi exibido hoje, não mostra novamente
    if(
        localStorage.getItem('tl_avatar_interagiu') === 'sim' ||
        localStorage.getItem('tl_avatar_ocultado') === 'sim'
    ){
        avatarContainer.style.display = 'none';
        return;
    }

    // Abre automaticamente
    setTimeout(function(){
        chatbox.style.display = 'block';
    }, TEMPO_ABRIR);

    // Fecha automaticamente
    setTimeout(function(){
        chatbox.style.display = 'none';
    }, TEMPO_FECHAR);

    // Esconde completamente
    setTimeout(function(){

        avatarContainer.classList.add('tl-hidden');

        localStorage.setItem(
            'tl_avatar_ocultado',
            'sim'
        );

        setTimeout(function(){
            avatarContainer.style.display = 'none';
        }, 500);

    }, TEMPO_SUMIR);

    // Clique no avatar
    avatarButton.addEventListener('click', function(){

        localStorage.setItem(
            'tl_avatar_interagiu',
            'sim'
        );

        if(chatbox.style.display === 'block'){

            chatbox.style.display = 'none';

        } else {

            chatbox.style.display = 'block';

            if(notification){
                notification.style.display = 'none';
            }

        }

    });

    // Botão X
    closeButton.addEventListener('click', function(){

        chatbox.style.display = 'none';

        localStorage.setItem(
            'tl_avatar_interagiu',
            'sim'
        );

    });

});

</script>

<?php } ?>