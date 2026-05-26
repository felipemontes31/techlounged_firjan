<?php

require_once(__DIR__ . "/../config/app.php");

function redirecionarPagina($mensagem, $pagina)
{
    echo "
    <script>

        alert(" . json_encode($mensagem) . ");

        window.location.href = " . json_encode(BASE_URL . $pagina) . ";

    </script>
    ";

    exit;
}

?>