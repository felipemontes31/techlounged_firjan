<?php

function respostaJSON($sucesso, $mensagem, $dados = [])
{
    header('Content-Type: application/json');

    echo json_encode([
        "sucesso" => $sucesso,
        "mensagem" => $mensagem,
        "dados" => $dados
    ]);

    exit;
}

?>