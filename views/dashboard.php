<?php

// require_once("../middleware/auth.php");
require_once("../config/conexao.php");

?>

<?php
// CADASTRAR
if (isset($_POST['cadastrar'])) {

    $nome = $_POST['nome_projeto'];
    $objetivo = $_POST['objetivo'];
    $eixo = $_POST['id_eixo'];
    $periodicidade = $_POST['id_periodicidade'];
    $publico = $_POST['id_publico_alvo'];
    $publicoSistema = $_POST['eh_publico'];
    $status = $_POST['status'];

    $sql = "INSERT INTO atividade(
        id_eixo,
        id_periodicidade,
        id_publico_alvo,
        nome_projeto,
        objetivo,
        eh_publico,
        status
    ) VALUES (
        '$eixo',
        '$periodicidade',
        '$publico',
        '$nome',
        '$objetivo',
        '$publicoSistema',
        '$status'
    )";

    $conexao->query($sql);
}


// =====================================
// EXCLUIR
// =====================================
if (isset($_GET['excluir'])) {

    $id = $_GET['excluir'];

    $conexao->query("
        DELETE FROM atividade 
        WHERE id = '$id'
    ");
}


// =====================================
// EDITAR
// =====================================
if (isset($_POST['editar'])) {

    $id = $_POST['id'];

    $nome = $_POST['nome_projeto'];
    $objetivo = $_POST['objetivo'];
    $eixo = $_POST['id_eixo'];
    $periodicidade = $_POST['id_periodicidade'];
    $publico = $_POST['id_publico_alvo'];
    $publicoSistema = $_POST['eh_publico'];
    $status = $_POST['status'];

    $sql = "
        UPDATE atividade SET

        id_eixo = '$eixo',
        id_periodicidade = '$periodicidade',
        id_publico_alvo = '$publico',
        nome_projeto = '$nome',
        objetivo = '$objetivo',
        eh_publico = '$publicoSistema',
        status = '$status'

        WHERE id = '$id'
    ";

    $conexao->query($sql);
}


// =====================================
// CONSULTAS DASHBOARD
// =====================================

$totalAtividades = $conexao->query("
    SELECT COUNT(*) as total 
    FROM atividade
")->fetch_assoc()['total'];

$totalExecucoes = $conexao->query("
    SELECT COUNT(*) as total 
    FROM registro_atividade
")->fetch_assoc()['total'];

$totalConcluidos = $conexao->query("
    SELECT COUNT(*) as total 
    FROM registro_atividade 
    WHERE status = 'Concluído'
")->fetch_assoc()['total'];

$publicoTotal = $conexao->query("
    SELECT SUM(publico_realizado) as total 
    FROM registro_atividade
")->fetch_assoc()['total'];

if (!$publicoTotal) {
    $publicoTotal = 0;
}


// =====================================
// LISTAGEM
// =====================================

$atividades = $conexao->query("

    SELECT 
        a.*,
        e.nome_eixo,
        p.descricao,
        pa.nome_publico

    FROM atividade a

    INNER JOIN eixo e 
    ON e.id = a.id_eixo

    INNER JOIN periodicidade p 
    ON p.id = a.id_periodicidade

    INNER JOIN publico_alvo pa 
    ON pa.id = a.id_publico_alvo

    ORDER BY a.id DESC

");


// =====================================
// SELECTS AUXILIARES
// =====================================

$eixos = $conexao->query("SELECT * FROM eixo");
$periodicidades = $conexao->query("SELECT * FROM periodicidade");
$publicos = $conexao->query("SELECT * FROM publico_alvo");

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard TechLounge</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial;
        }

        .topo {
            background: #1e293b;
            color: white;
            padding: 20px;
        }

        .card-dashboard {
            border: none;
            border-radius: 12px;
            color: white;
            padding: 20px;
        }

        .bg1 {
            background: #2563eb;
        }

        .bg2 {
            background: #059669;
        }

        .bg3 {
            background: #dc2626;
        }

        .bg4 {
            background: #7c3aed;
        }
    </style>

</head>

<body>

<div class="topo">
<div class="container d-flex justify-content-between align-items-center">
<div>
<h2>📚 Dashboard - TechLounge</h2>
<p class="mb-0">
Sistema de Gestão da Biblioteca
</p>
</div>
<a href="../index.php" class="btn btn-outline-light d-flex align-items-center gap-2">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
</svg>
Voltar
</a>
</div>
</div>

    <div class="container mt-4">

        <div class="row g-4">

            <div class="col-md-3">

                <div class="card-dashboard bg1">

                    <h5>Total Atividades</h5>

                    <h2><?= $totalAtividades ?></h2>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card-dashboard bg2">

                    <h5>Total Execuções</h5>

                    <h2><?= $totalExecucoes ?></h2>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card-dashboard bg3">

                    <h5>Eventos Concluídos</h5>

                    <h2><?= $totalConcluidos ?></h2>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card-dashboard bg4">

                    <h5>Público Total</h5>

                    <h2><?= $publicoTotal ?></h2>

                </div>

            </div>

        </div>


        <div class="card mt-5 shadow">

            <div class="card-header bg-dark text-white">

                ➕ Nova Atividade

            </div>

            <div class="card-body">

                <form method="POST">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label>Nome Projeto</label>

                            <input type="text"
                                name="nome_projeto"
                                class="form-control"
                                required>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label>Eixo</label>

                            <select name="id_eixo"
                                class="form-control"
                                required>

                                <option value="">Selecione</option>

                                <?php while ($e = $eixos->fetch_assoc()) { ?>

                                    <option value="<?= $e['id'] ?>">

                                        <?= $e['nome_eixo'] ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label>Periodicidade</label>

                            <select name="id_periodicidade"
                                class="form-control"
                                required>

                                <option value="">Selecione</option>

                                <?php while ($p = $periodicidades->fetch_assoc()) { ?>

                                    <option value="<?= $p['id'] ?>">

                                        <?= $p['descricao'] ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label>Público Alvo</label>

                            <select name="id_publico_alvo"
                                class="form-control"
                                required>

                                <option value="">Selecione</option>

                                <?php while ($pa = $publicos->fetch_assoc()) { ?>

                                    <option value="<?= $pa['id'] ?>">

                                        <?= $pa['nome_publico'] ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>


                        <div class="col-md-12 mb-3">

                            <label>Objetivo</label>

                            <textarea name="objetivo"
                                class="form-control"
                                rows="3"></textarea>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label>Visibilidade</label>

                            <select name="eh_publico"
                                class="form-control">

                                <option value="1">Público</option>
                                <option value="0">Interno</option>

                            </select>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label>Status da Atividade</label>

                            <select name="status"
                                class="form-control"
                                required>

                                <option value="Ativa">Ativa</option>

                                <option value="Inativa">Inativa</option>

                                <option value="Planejamento">Planejamento</option>

                            </select>

                        </div>

                    </div>

                    <button type="submit"
                        name="cadastrar"
                        class="btn btn-success">

                        Salvar Atividade

                    </button>

                </form>

            </div>
        </div>


       <div class="card mt-5 shadow">

    <div class="card-header bg-primary text-white">
        📋 Gerenciamento de Atividades
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">

                    <tr>
                        <th>ID</th>
                        <th>Projeto</th>
                        <th>Eixo</th>
                        <th>Periodicidade</th>
                        <th>Público</th>
                        <th>Status</th>
                        <th>Visibilidade</th>
                        <th width="220">Ações</th>
                    </tr>

                </thead>

                <tbody>

                <?php while($a = $atividades->fetch_assoc()) { ?>

                    <tr>

                        <td><?= $a['id'] ?></td>

                        <td style="min-width:200px;">
                            <?= $a['nome_projeto'] ?>
                        </td>

                        <td>
                            <?= $a['nome_eixo'] ?>
                        </td>

                        <td>
                            <?= $a['descricao'] ?>
                        </td>

                        <td>
                            <?= $a['nome_publico'] ?>
                        </td>

                        <td>

                            <?php if($a['status'] == 'Ativa'){ ?>

                                <span class="badge bg-success">
                                    <?= $a['status'] ?>
                                </span>

                            <?php } elseif($a['status'] == 'Inativa'){ ?>

                                <span class="badge bg-danger">
                                    <?= $a['status'] ?>
                                </span>

                            <?php } else { ?>

                                <span class="badge bg-warning text-dark">
                                    <?= $a['status'] ?>
                                </span>

                            <?php } ?>

                        </td>

                        <td>

                            <?php if($a['eh_publico'] == 1){ ?>

                                <span class="badge bg-primary">
                                    Público
                                </span>

                            <?php } else { ?>

                                <span class="badge bg-secondary">
                                    Interno
                                </span>

                            <?php } ?>

                        </td>

                        <td>

                            <div class="d-flex gap-2 flex-wrap">

                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal<?= $a['id'] ?>">

                                    Editar

                                </button>

                                <a href="?excluir=<?= $a['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Deseja excluir esta atividade?')">

                                     Excluir

                                </a>

                            </div>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>