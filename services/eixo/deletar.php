<?php

require_once(__DIR__ . "/../../middleware/permissao.php");
require_once(__DIR__ . "/../../config/conexao.php");
require_once(__DIR__ . "/../../utils/json.php");

verificarPermissao([
    "Administrador",
    "Bibliotecário"
]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respostaJSON(false, "Método inválido.");
}

$id = $_POST['id'] ?? null;

if (empty($id)) {
    respostaJSON(false, "ID não informado.");
}

$sqlAtividades = "
SELECT id
FROM atividade
WHERE id_eixo = ?
LIMIT 1
";

$stmtAtividades = $conexao->prepare($sqlAtividades);

$stmtAtividades->bind_param(
    "i",
    $id
);

$stmtAtividades->execute();

$resultadoAtividades = $stmtAtividades->get_result();

if ($resultadoAtividades->num_rows > 0) {

    respostaJSON(
        false,
        "Não é possível excluir um eixo vinculado a atividades."
    );
}

$sql = "
DELETE FROM eixo
WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        respostaJSON(
            true,
            "Eixo removido com sucesso."
        );
    }
}

respostaJSON(
    false,
    "Eixo não encontrado."
);

?>