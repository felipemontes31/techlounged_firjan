<?php

require_once("../middleware/auth.php");
require_once("../config/conexao.php");

$sql = "
SELECT

    ra.id,
    ra.tema_especifico,
    ra.data_execucao,
    a.nome_projeto

FROM registro_atividade ra

INNER JOIN atividade a
ON ra.id_atividade = a.id

WHERE a.nome_projeto = 'Cine Biblioteca'

ORDER BY ra.data_execucao DESC
";

$resultado = $conexao->query($sql);

?>

<form
    action="../services/cine_biblioteca/criar.php"
    method="POST"
>

    <label>
        Registro Cine Biblioteca
    </label>

    <select
        name="id_registro_atividade"
        required
    >

        <option value="">
            Selecione
        </option>

        <?php while($registro = $resultado->fetch_assoc()) : ?>

            <option value="<?= $registro['id'] ?>">

                <?= $registro['tema_especifico'] ?>

                -

                <?= $registro['data_execucao'] ?>

            </option>

        <?php endwhile; ?>

    </select>

    <br><br>

    <input
        type="text"
        name="titulo_curta"
        placeholder="Título do curta"
        required
    >

    <br><br>

    <input
        type="url"
        name="link"
        placeholder="Link"
    >

    <br><br>

    <textarea
        name="detalhes_controle"
        placeholder="Detalhes"
    ></textarea>

    <br><br>

    <button type="submit">
        Salvar
    </button>

</form>