<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . "/../config/conexao.php");
require_once(__DIR__ . "/../utils/json.php");
require_once(__DIR__ . "/../models/Curso.php");

$acao = $_GET['acao'] ?? '';

if (!isset($_SESSION['usuario'])) {
    respostaJSON(false, "Faça login.");
}

$funcaoUsuario = $_SESSION['usuario']['funcao'] ?? '';
$funcoesPermitidas = ['Administrador', 'Bibliotecário', 'Bibliotecario'];

if (!in_array($funcaoUsuario, $funcoesPermitidas, true)) {
    respostaJSON(false, "Sem permissão para gerenciar cursos.");
}

$model = new Curso($conexao);

function normalizarTextoCurso($valor): string
{
    return trim((string)($valor ?? ''));
}

function valorOuNullCurso($valor): ?string
{
    $texto = normalizarTextoCurso($valor);
    return $texto === '' ? null : $texto;
}

switch ($acao) {
    case 'listar':
        respostaJSON(true, "Cursos carregados.", $model->listarTodos());
        break;

    case 'buscar':
        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            respostaJSON(false, "ID inválido.");
        }

        $curso = $model->buscarPorId($id);

        if (!$curso) {
            respostaJSON(false, "Curso não encontrado.");
        }

        respostaJSON(true, "Curso encontrado.", $curso);
        break;

    case 'criar':
        $nomeCurso = normalizarTextoCurso($_POST['nome_curso'] ?? '');
        $descricao = valorOuNullCurso($_POST['descricao'] ?? null);

        if ($nomeCurso === '') {
            respostaJSON(false, "O nome do curso é obrigatório.");
        }

        if (mb_strlen($nomeCurso) > 50) {
            respostaJSON(false, "O nome do curso deve ter no máximo 50 caracteres.");
        }

        if ($model->nomeExiste($nomeCurso)) {
            respostaJSON(false, "Já existe um curso com esse nome.");
        }

        $sucesso = $model->criar($nomeCurso, $descricao);
        respostaJSON($sucesso, $sucesso ? "Curso cadastrado com sucesso." : "Erro ao cadastrar curso.");
        break;

    case 'atualizar':
        $id = intval($_POST['id'] ?? 0);
        $nomeCurso = normalizarTextoCurso($_POST['nome_curso'] ?? '');
        $descricao = valorOuNullCurso($_POST['descricao'] ?? null);

        if ($id <= 0) {
            respostaJSON(false, "ID inválido.");
        }

        if ($nomeCurso === '') {
            respostaJSON(false, "O nome do curso é obrigatório.");
        }

        if (mb_strlen($nomeCurso) > 50) {
            respostaJSON(false, "O nome do curso deve ter no máximo 50 caracteres.");
        }

        if ($model->nomeExiste($nomeCurso, $id)) {
            respostaJSON(false, "Já existe outro curso com esse nome.");
        }

        $sucesso = $model->atualizar($id, $nomeCurso, $descricao);
        respostaJSON($sucesso, $sucesso ? "Curso atualizado com sucesso." : "Erro ao atualizar curso.");
        break;

    case 'deletar':
        $id = intval($_GET['id'] ?? 0);

        if ($id <= 0) {
            respostaJSON(false, "ID inválido.");
        }

        $sucesso = $model->deletar($id);
        respostaJSON(
            $sucesso,
            $sucesso
                ? "Curso excluído com sucesso."
                : "Não foi possível excluir. O curso pode estar vinculado a um ou mais usuários."
        );
        break;

    default:
        respostaJSON(false, "Ação não reconhecida.");
        break;
}
