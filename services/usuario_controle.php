<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . "/../config/conexao.php");
require_once(__DIR__ . "/../utils/json.php");

$acao = $_GET['acao'] ?? '';

if (!isset($_SESSION['usuario'])) {
    respostaJSON(false, "Faça login.");
}

$usuarioSessao = $_SESSION['usuario'];
$idUsuarioLogado = intval($usuarioSessao['id'] ?? 0);
$funcaoSessao = $usuarioSessao['funcao'] ?? $usuarioSessao['nome_funcao'] ?? '';
$idFuncaoSessao = intval($usuarioSessao['id_funcao'] ?? 0);
$isAdmin = ($funcaoSessao === 'Administrador' || $idFuncaoSessao === 1);

function exigirAdmin($isAdmin)
{
    if (!$isAdmin) {
        respostaJSON(false, "Sem permissão.");
    }
}

function normalizarTexto($valor)
{
    return trim((string)($valor ?? ''));
}

function valorOuNull($valor)
{
    $texto = normalizarTexto($valor);
    return $texto === '' ? null : $texto;
}

function emailExiste(mysqli $conexao, string $email, int $idIgnorar = 0): bool
{
    $sql = "SELECT id FROM usuario WHERE email = ?";
    $tipos = "s";
    $valores = [$email];

    if ($idIgnorar > 0) {
        $sql .= " AND id <> ?";
        $tipos .= "i";
        $valores[] = $idIgnorar;
    }

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param($tipos, ...$valores);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function matriculaExiste(mysqli $conexao, ?string $matricula, int $idIgnorar = 0): bool
{
    if ($matricula === null || $matricula === '') {
        return false;
    }

    $sql = "SELECT id FROM usuario WHERE matricula = ?";
    $tipos = "s";
    $valores = [$matricula];

    if ($idIgnorar > 0) {
        $sql .= " AND id <> ?";
        $tipos .= "i";
        $valores[] = $idIgnorar;
    }

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param($tipos, ...$valores);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function validarSexo(string $sexo): string
{
    $permitidos = ['Masculino', 'Feminino', 'Prefiro não informar'];
    return in_array($sexo, $permitidos, true) ? $sexo : 'Prefiro não informar';
}

switch ($acao) {
    case 'listar_funcoes':
        exigirAdmin($isAdmin);

        $resultado = $conexao->query("SELECT id, nome_funcao, descricao FROM funcao ORDER BY id ASC");
        $dados = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

        respostaJSON(true, "Funções encontradas.", $dados);
        break;

    case 'listar':
        exigirAdmin($isAdmin);

        $sql = "
            SELECT
                u.id,
                u.id_funcao,
                f.nome_funcao,
                u.nome,
                u.sobrenome,
                u.sexo,
                u.email,
                u.matricula,
                u.ativo,
                u.data_cadastro
            FROM usuario u
            INNER JOIN funcao f ON f.id = u.id_funcao
            ORDER BY u.nome ASC, u.sobrenome ASC, u.id ASC
        ";

        $resultado = $conexao->query($sql);
        $dados = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

        respostaJSON(true, "Usuários encontrados.", $dados);
        break;

    case 'buscar':
        $id = intval($_GET['id'] ?? 0);

        if (!$isAdmin && $id !== $idUsuarioLogado) {
            respostaJSON(false, "Sem permissão.");
        }

        $sql = "
            SELECT
                u.id,
                u.id_funcao,
                f.nome_funcao,
                u.nome,
                u.sobrenome,
                u.sexo,
                u.email,
                u.matricula,
                u.ativo,
                u.data_cadastro
            FROM usuario u
            INNER JOIN funcao f ON f.id = u.id_funcao
            WHERE u.id = ?
        ";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        if (!$usuario) {
            respostaJSON(false, "Usuário não encontrado.");
        }

        respostaJSON(true, "Usuário encontrado.", $usuario);
        break;

    case 'criar':
        exigirAdmin($isAdmin);

        $nome = normalizarTexto($_POST['nome'] ?? '');
        $sobrenome = valorOuNull($_POST['sobrenome'] ?? null);
        $sexo = validarSexo(normalizarTexto($_POST['sexo'] ?? 'Prefiro não informar'));
        $email = normalizarTexto($_POST['email'] ?? '');
        $matricula = valorOuNull($_POST['matricula'] ?? null);
        $senha = (string)($_POST['senha'] ?? '');
        $idFuncao = intval($_POST['id_funcao'] ?? 0);
        $ativo = intval($_POST['ativo'] ?? 1) === 1 ? 1 : 0;

        if ($nome === '' || $email === '' || $senha === '' || $idFuncao <= 0) {
            respostaJSON(false, "Nome, e-mail, senha e perfil são obrigatórios.");
        }

        if (strlen($nome) > 30) {
            respostaJSON(false, "O nome deve ter no máximo 30 caracteres.");
        }

        if ($sobrenome !== null && strlen($sobrenome) > 100) {
            respostaJSON(false, "O sobrenome deve ter no máximo 100 caracteres.");
        }

        if ($matricula !== null && strlen($matricula) > 10) {
            respostaJSON(false, "A matrícula deve ter no máximo 10 caracteres.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respostaJSON(false, "Informe um e-mail válido.");
        }

        if (emailExiste($conexao, $email)) {
            respostaJSON(false, "E-mail já cadastrado.");
        }

        if (matriculaExiste($conexao, $matricula)) {
            respostaJSON(false, "Matrícula já cadastrada.");
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO usuario
                (id_funcao, nome, sobrenome, sexo, email, matricula, senha_hash, ativo)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("issssssi", $idFuncao, $nome, $sobrenome, $sexo, $email, $matricula, $senhaHash, $ativo);
        $sucesso = $stmt->execute();

        respostaJSON($sucesso, $sucesso ? "Usuário criado." : "Erro ao criar usuário.");
        break;

    case 'editar':
        $id = intval($_POST['id'] ?? 0);

        if (!$isAdmin && $id !== $idUsuarioLogado) {
            respostaJSON(false, "Sem permissão.");
        }

        $nome = normalizarTexto($_POST['nome'] ?? '');
        $sobrenome = valorOuNull($_POST['sobrenome'] ?? null);
        $sexo = validarSexo(normalizarTexto($_POST['sexo'] ?? 'Prefiro não informar'));
        $email = normalizarTexto($_POST['email'] ?? '');
        $matricula = valorOuNull($_POST['matricula'] ?? null);
        $senha = (string)($_POST['senha'] ?? '');

        if ($nome === '' || $email === '') {
            respostaJSON(false, "Nome e e-mail são obrigatórios.");
        }

        if (strlen($nome) > 30) {
            respostaJSON(false, "O nome deve ter no máximo 30 caracteres.");
        }

        if ($sobrenome !== null && strlen($sobrenome) > 100) {
            respostaJSON(false, "O sobrenome deve ter no máximo 100 caracteres.");
        }

        if ($matricula !== null && strlen($matricula) > 10) {
            respostaJSON(false, "A matrícula deve ter no máximo 10 caracteres.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respostaJSON(false, "Informe um e-mail válido.");
        }

        if (emailExiste($conexao, $email, $id)) {
            respostaJSON(false, "E-mail já utilizado.");
        }

        if (matriculaExiste($conexao, $matricula, $id)) {
            respostaJSON(false, "Matrícula já utilizada.");
        }

        if ($isAdmin) {
            $idFuncao = intval($_POST['id_funcao'] ?? 0);
            $ativo = intval($_POST['ativo'] ?? 1) === 1 ? 1 : 0;

            if ($idFuncao <= 0) {
                respostaJSON(false, "Perfil inválido.");
            }

            if ($senha !== '') {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "
                    UPDATE usuario
                    SET id_funcao = ?, nome = ?, sobrenome = ?, sexo = ?, email = ?, matricula = ?, senha_hash = ?, ativo = ?
                    WHERE id = ?
                ";
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param("issssssii", $idFuncao, $nome, $sobrenome, $sexo, $email, $matricula, $senhaHash, $ativo, $id);
            } else {
                $sql = "
                    UPDATE usuario
                    SET id_funcao = ?, nome = ?, sobrenome = ?, sexo = ?, email = ?, matricula = ?, ativo = ?
                    WHERE id = ?
                ";
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param("isssssii", $idFuncao, $nome, $sobrenome, $sexo, $email, $matricula, $ativo, $id);
            }
        } else {
            if ($senha !== '') {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "
                    UPDATE usuario
                    SET nome = ?, sobrenome = ?, sexo = ?, email = ?, matricula = ?, senha_hash = ?
                    WHERE id = ?
                ";
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param("ssssssi", $nome, $sobrenome, $sexo, $email, $matricula, $senhaHash, $id);
            } else {
                $sql = "
                    UPDATE usuario
                    SET nome = ?, sobrenome = ?, sexo = ?, email = ?, matricula = ?
                    WHERE id = ?
                ";
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param("sssssi", $nome, $sobrenome, $sexo, $email, $matricula, $id);
            }
        }

        $sucesso = $stmt->execute();

        if ($sucesso && $id === $idUsuarioLogado) {
            $_SESSION['usuario']['nome'] = $nome;
            $_SESSION['usuario']['sobrenome'] = $sobrenome;
            $_SESSION['usuario']['sexo'] = $sexo;
            $_SESSION['usuario']['email'] = $email;
            $_SESSION['usuario']['matricula'] = $matricula;
            if ($isAdmin) {
                $_SESSION['usuario']['id_funcao'] = $idFuncao;
                $_SESSION['usuario']['ativo'] = $ativo;
            }
        }

        respostaJSON($sucesso, $sucesso ? "Usuário atualizado." : "Erro ao atualizar usuário.");
        break;

    case 'excluir':
        exigirAdmin($isAdmin);

        $id = intval($_POST['id'] ?? 0);

        if ($id <= 0) {
            respostaJSON(false, "ID inválido.");
        }

        if ($id === $idUsuarioLogado) {
            respostaJSON(false, "Você não pode inativar sua própria conta por esta tela.");
        }

        $sql = "UPDATE usuario SET ativo = 0 WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $sucesso = $stmt->execute();

        respostaJSON($sucesso, $sucesso ? "Usuário inativado." : "Erro ao inativar usuário.");
        break;

    default:
        respostaJSON(false, "Ação não reconhecida.");
        break;
}
