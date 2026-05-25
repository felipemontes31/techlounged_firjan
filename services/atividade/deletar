<?php

require_once("../../config/conexao.php");

$id = $_GET['id'];

$sql = "DELETE FROM atividade WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

if (!$stmt->execute()) {

    respostaJSON(false, "Erro ao deletar atividade");

}

respostaJSON(true, "Atividade deletada");

?>