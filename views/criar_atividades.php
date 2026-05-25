<?php

require_once("../middleware/permissao.php");

verificarPermissao([
    "Administrador",
    "Bibliotecário"
]);

?>

<h1>
    Bem-vindo a criação de atividade,
    <?= $_SESSION['usuario']['nome'] ?>
</h1>

<a href="../services/auth/logout.php">
    Sair
</a>