<?php

class Response
{
    public static function json(
        $sucesso,
        $mensagem,
        $dados = [],
        $status = 200
    ) {

        http_response_code($status);

        header('Content-Type: application/json');

        echo json_encode([
            "sucesso" => $sucesso,
            "mensagem" => $mensagem,
            "dados" => $dados
        ]);

        exit;
    }
}

?>