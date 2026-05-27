<?php

require_once("../middleware/auth.php");

?>

<h1>
    Bem-vindo,
    <?= $_SESSION['usuario']['nome']?>
    (Email: <?= $_SESSION['usuario']['email']?>)
</h1>

<a href="../services/auth/logout.php">
    Sair
</a>