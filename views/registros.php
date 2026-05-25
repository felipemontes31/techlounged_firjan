<?php

require_once("../config/conexao.php");
//require_once("../middleware/auth.php");

$sqlAtividades = "
SELECT
    id,
    nome_projeto
FROM atividade
ORDER BY nome_projeto
";

$resultadoAtividades = $conexao->query($sqlAtividades);

$sqlEspacos = "
SELECT
    id,
    nome_espaco
FROM espaco
ORDER BY nome_espaco
";

$resultadoEspacos = $conexao->query($sqlEspacos);

?>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">
<form
    action="../services/registro_atividade/criar.php"
    method="POST"
>

    <label>
        Atividade
    </label>

    <select name="id_atividade" required>

        <option value="">
            Selecione
        </option>

        <?php while($atividade = $resultadoAtividades->fetch_assoc()) : ?>

            <option value="<?= $atividade['id'] ?>">

                <?= $atividade['nome_projeto'] ?>

            </option>

        <?php endwhile; ?>

    </select>

    <br><br>

    <label>
        Espaço
    </label>

    <select name="id_espaco" required>

        <option value="">
            Selecione
        </option>

        <?php while($espaco = $resultadoEspacos->fetch_assoc()) : ?>

            <option value="<?= $espaco['id'] ?>">

                <?= $espaco['nome_espaco'] ?>

            </option>

        <?php endwhile; ?>

    </select>

    <br><br>

    <label>
        Data execução
    </label>

    <input
        type="date"
        name="data_execucao"
        required
    >

    <br><br>

    <label>
        Data finalização
    </label>

    <input
        type="date"
        name="data_finalizacao"
    >

    <br><br>

    <label>
        Tema específico
    </label>

    <input
        type="text"
        name="tema_especifico"
    >

    <br><br>

    <label>
        Status
    </label>

    <select name="status">

        <option value="Planejado">
            Planejado
        </option>

        <option value="Concluído">
            Concluído
        </option>

        <option value="Cancelado">
            Cancelado
        </option>

    </select>

    <br><br>

    <label>
        Público previsto
    </label>

    <input
        type="number"
        name="publico_previsto"
    >

    <br><br>

    <button type="submit">
        Salvar
    </button>

</form>
<a href="../services/auth/logout.php">
    Sair
</a>