<?php

require_once("auth.php");
require_once(__DIR__ . "/../utils/response.php");

function verificarPermissao($funcoesPermitidas = [])
{
    $funcaoUsuario = $_SESSION['usuario']['funcao'];

    if (!in_array($funcaoUsuario, $funcoesPermitidas)) {

        redirecionarPagina(
            "Você não possui permissão para acessar esta página.",
            "/techlounged/views/dashboard.php"
        );
    }
}

?>

?>