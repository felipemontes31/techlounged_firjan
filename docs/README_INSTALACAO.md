# Frontend modernizado do TechLounged

## Estrutura criada

- `assets/css/techlounged.css`: design system com a paleta institucional Sistema Indústria.
- `assets/js/techlounged.js`: funções pequenas e reutilizáveis de menu, modal, escape de texto e data BR.
- `includes/topo.php`: topo público com navegação por perfil de usuário.
- `includes/admin_sidebar.php`: menu lateral administrativo protegido por `middleware/permissao.php`.
- `views/eventos.php`: página principal estilo site de eventos/ingressos.
- `views/perfil.php`: área do usuário para gestão dos próprios dados.
- `views/minhas_inscricoes.php`: área do usuário para acompanhar inscrições.
- `views/admin/*.php`: telas administrativas modernizadas.

## Como aplicar

Copie as pastas `assets`, `includes` e `views` para a raiz do projeto `techlounged`.

A estrutura esperada é:

```txt
techlounged/
  assets/
    css/techlounged.css
    js/techlounged.js
  config/
    app.php
    conexao.php
  includes/
    topo.php
    admin_sidebar.php
  middleware/
    auth.php
    permissao.php
  services/
    ... seus controllers atuais ...
  views/
    eventos.php
    login.php
    cadastro.php
    perfil.php
    minhas_inscricoes.php
    admin/
      atividade.php
      registros.php
      cine_biblioteca.php
      eixo.php
      espaco.php
      periodicidade.php
      publico_alvo.php
```

## Endpoints esperados

O frontend reaproveita seus endpoints atuais:

- `services/registro_atividade_controle.php`
- `services/atividade_controle.php`
- `services/periodicidade_controle.php`
- `services/publico_alvo_controle.php`
- `services/espaco_controle.php`
- `services/cinebiblioteca_controle.php`

Para as páginas novas, recomendo criar estes endpoints caso ainda não existam:

- `services/registro_atividade_controle.php?acao=minhas_inscricoes`
- `services/auth/atualizar_perfil.php`

## Proteção de páginas administrativas

As páginas administrativas chamam `middleware/permissao.php` e usam:

```php
verificarPermissao(['Administrador', 'Bibliotecário', 'Bibliotecario']);
```

Caso a sessão guarde apenas `id_funcao`, ajuste o middleware para mapear os IDs para nomes ou aceite IDs diretamente.

## Criar pasta config

### Arquivos base da pasta

#### app.php
```php
<?php

// ======================================================
// URL BASE DO PROJETO
// ======================================================

define("BASE_URL", "/techlounged");

// ======================================================
// CAMINHO FÍSICO DO PROJETO
// ======================================================

define("BASE_PATH", dirname(__DIR__));

?>
```

#### conexao.php
```php
<?php

$host = "127.0.0.1";
$usuario = "root";
$senha = "";
$banco = "techlounged";

$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

?>
```