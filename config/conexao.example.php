<?php

$host = "127.0.0.1";
$usuario = "root";
$senha = "";
$banco = "techlounged";

$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

?>