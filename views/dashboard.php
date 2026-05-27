<?php

require_once("../middleware/auth.php");

?>

<!-- <h1>
    Bem-vindo,
    <?= $_SESSION['usuario']['nome']?>
    (Email: <?= $_SESSION['usuario']['email']?>)
</h1>

<a href="../services/auth/logout.php">
    Sair
</a> -->
<?php
// dashboard.php
// Dashboard simples e funcional - Sistema TechLounge

// ===============================
// CONEXÃO COM BANCO
// ===============================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "techlounge";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// ===============================
// CONSULTAS
// ===============================

// Total de atividades
$totalAtividades = $conn->query("SELECT COUNT(*) as total FROM atividade")
    ->fetch_assoc()['total'];

// Total de execuções
$totalExecucoes = $conn->query("SELECT COUNT(*) as total FROM registro_atividade")
    ->fetch_assoc()['total'];

// Eventos concluídos
$totalConcluidos = $conn->query("
    SELECT COUNT(*) as total 
    FROM registro_atividade 
    WHERE status = 'Concluído'
")->fetch_assoc()['total'];

// Público total realizado
$publicoTotal = $conn->query("
    SELECT SUM(publico_realizado) as total 
    FROM registro_atividade
")->fetch_assoc()['total'];

if (!$publicoTotal) {
    $publicoTotal = 0;
}

// Próximas atividades
$proximas = $conn->query("
    SELECT 
        ra.id,
        a.nome_projeto,
        ra.tema_especifico,
        ra.data_execucao,
        ra.status,
        e.nome_espaco
    FROM registro_atividade ra
    INNER JOIN atividade a ON a.id = ra.id_atividade
    INNER JOIN espaco e ON e.id = ra.id_espaco
    ORDER BY ra.data_execucao ASC
    LIMIT 8
");

// Dados para gráfico simples
$grafico = $conn->query("
    SELECT 
        ex.nome_eixo,
        COUNT(ra.id) as total
    FROM registro_atividade ra
    INNER JOIN atividade a ON a.id = ra.id_atividade
    INNER JOIN eixo ex ON ex.id = a.id_eixo
    GROUP BY ex.nome_eixo
");

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard - TechLounge</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
    font-family:Arial;
}

.topo{
    background:#1e293b;
    color:white;
    padding:20px;
}

.card-dashboard{
    border:none;
    border-radius:12px;
    color:white;
    padding:20px;
}

.bg1{
    background:#2563eb;
}

.bg2{
    background:#059669;
}

.bg3{
    background:#dc2626;
}

.bg4{
    background:#7c3aed;
}

.table{
    background:white;
}

.grafico-barra{
    height:30px;
    background:#2563eb;
    border-radius:8px;
    color:white;
    padding-left:10px;
    line-height:30px;
}

</style>

</head>
<body>

<div class="topo">
    <h2>📚 Dashboard - Sistema TechLounged</h2>
    <p>Controle de atividades pedagógicas, culturais e gestão da biblioteca</p>
</div>

<div class="container mt-4">

    <!-- CARDS -->
    <div class="row g-4">

        <div class="col-md-3">
            <div class="card-dashboard bg1">
                <h5>Total de Atividades</h5>
                <h2><?= $totalAtividades ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-dashboard bg2">
                <h5>Total de Execuções</h5>
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
                <h5>Público Alcançado</h5>
                <h2><?= $publicoTotal ?></h2>
            </div>
        </div>

    </div>

    <!-- TABELA -->
    <div class="row mt-5">

        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    Próximas Execuções de Atividades
                </div>

                <div class="card-body">

                    <table class="table table-bordered table-hover">

                        <thead class="table-dark">
                            <tr>
                                <th>Projeto</th>
                                <th>Tema</th>
                                <th>Data</th>
                                <th>Espaço</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php while($row = $proximas->fetch_assoc()) { ?>

                            <tr>
                                <td><?= $row['nome_projeto'] ?></td>

                                <td><?= $row['tema_especifico'] ?></td>

                                <td>
                                    <?= date('d/m/Y', strtotime($row['data_execucao'])) ?>
                                </td>

                                <td><?= $row['nome_espaco'] ?></td>

                                <td>

                                    <?php if($row['status'] == 'Concluído') { ?>

                                        <span class="badge bg-success">
                                            <?= $row['status'] ?>
                                        </span>

                                    <?php } else { ?>

                                        <span class="badge bg-warning text-dark">
                                            <?= $row['status'] ?>
                                        </span>

                                    <?php } ?>

                                </td>
                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>
            </div>

        </div>

        <!-- GRÁFICO SIMPLES -->
        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Execuções por Eixo
                </div>

                <div class="card-body">

                <?php while($g = $grafico->fetch_assoc()) { ?>

                    <p class="mb-1">
                        <strong><?= $g['nome_eixo'] ?></strong>
                    </p>

                    <div class="grafico-barra mb-3"
                         style="width: <?= $g['total'] * 20 ?>px">

                        <?= $g['total'] ?>

                    </div>

                <?php } ?>

                </div>

            </div>

        </div>

    </div>

    <!-- FUNCIONALIDADES -->
    <div class="row mt-5 mb-5">

        <div class="col-md-12">

            <div class="card shadow-sm">

                <div class="card-header bg-secondary text-white">
                    Funcionalidades do Sistema
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4">
                            <h5>📌 Atividades</h5>
                            <ul>
                                <li>Cadastro de projetos</li>
                                <li>Controle por eixo</li>
                                <li>Periodicidade</li>
                                <li>Público-alvo</li>
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <h5>📅 Execuções</h5>
                            <ul>
                                <li>Registro por data</li>
                                <li>Status do evento</li>
                                <li>Controle de público</li>
                                <li>Gestão de espaços</li>
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <h5>🎬 Cine Biblioteca</h5>
                            <ul>
                                <li>Cadastro de curtas</li>
                                <li>Links das mídias</li>
                                <li>Detalhes da exibição</li>
                                <li>Histórico completo</li>
                            </ul>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>