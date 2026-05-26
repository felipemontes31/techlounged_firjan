<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../../utils/response.php");

session_destroy();

redirecionarPagina(
    "Logout realizado com sucesso.",
    "/views/login.php"
);

?>