# Backend Routes - Documentação (Markdown)
## Base: Todas as rotas usam ?acao=NOME via GET/POST. Retorno sempre JSON.
### 1. Usuários (/services/usuario_controle.php)

?acao=listar → Lista todos (admin apenas)
?acao=buscar&id=ID → Busca por ID (admin ou próprio usuário)
?acao=criar (POST) → Criar Usuário (admin apenas)
?acao=editar (POST) → Editar Usuário (admin apenas)
?acao=excluir (POST) → Excluir Usuário (admin apenas)

### 2. Registros de Atividade (/services/registro_atividade_controle.php)

?acao=listar → Lista registros
?acao=buscar&id=ID → Busca por ID

Padrão dos demais (pastas: atividade, auth, cine_blibioteca, eixo, funcao):
?acao=listar | buscar | cadastrar | editar | deletar

Acesse os arquivos em /services/ para parâmetros exatos.


### Ajuste links HTML
ANTES:
```html
<a href="/techlounged/views/login.php">
```

DEPOIS:
```php
<?php require_once("../config/app.php"); ?>

<a href="<?= BASE_URL ?>/views/login.php">
```

### Ajuste formulários

ANTES:
```html
<form action="/techlounged/actions/login.php">
```

DEPOIS:
```php
<form action="<?= BASE_URL ?>/actions/login.php">
```

### Ajuste JS Fetch

ANTES:
```javascript
fetch("/techlounged/controllers/usuario.php")
```

DEPOIS:
```php
<script>

const BASE_URL = "<?= BASE_URL ?>";

fetch(BASE_URL + "/controllers/usuario.php")

</script>
```

### Melhorar ainda mais (AUTO DETECT)

Você pode automatizar:

```php
define(
    "BASE_URL",
    dirname($_SERVER['SCRIPT_NAME'])
);
```