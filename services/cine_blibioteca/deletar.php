<?php

require_once(__DIR__ . "/../../middleware/auth.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respostaJSON(false, "Método inválido.");
}

$id = $_POST['id'] ?? null;

if (empty($id)) {
    respostaJSON(false, "ID não informado.");
}

$sql = "
DELETE cb
FROM cine_biblioteca cb

INNER JOIN registro_atividade ra
    ON ra.id = cb.id_registro_atividade

INNER JOIN atividade a
    ON a.id = ra.id_atividade

WHERE cb.id = ?
AND a.nome_projeto = 'Cine Biblioteca'
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        respostaJSON(
            true,
            "Registro removido com sucesso."
        );
    }
}

respostaJSON(
    false,
    "Registro não encontrado."
);

?>