<?php

function redirecionarPagina($mensagem, $pagina)
{
    echo "
    <script>

        alert(" . json_encode($mensagem) . ");

        window.location.href = " . json_encode($pagina) . ";

    </script>
    ";

    exit;
}

?>