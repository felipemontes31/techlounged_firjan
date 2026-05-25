<?php

session_start();

require_once(__DIR__ . "/../utils/response.php");

if (!isset($_SESSION['usuario'])) {

    redirecionarPagina(
        "Faça login para continuar.",
        "/techlounged/views/login.php"
    );
}

?>