<?php

session_start();

require_once("../../utils/response.php");

session_destroy();

redirecionarPagina(
    "Logout realizado com sucesso.",
    "/techlounged/views/login.php"
);

?>