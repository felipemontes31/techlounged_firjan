# TechLounged — Ajustes V5

## Recursos adicionados

1. **Modo escuro**
   - Botão no topo público.
   - Botão na barra lateral administrativa.
   - Preferência salva em `localStorage` com a chave `techlounged_tema`.

2. **Nova organização do menu administrativo**
   - A barra lateral agora exibe somente:
     - Registro de eventos
     - Cine Biblioteca
     - Gerenciamento do sistema
   - A tela `views/admin/gerenciamento_sistema.php` centraliza os submenus:
     - Atividades
     - Eixos
     - Espaços
     - Periodicidades
     - Público-alvo
     - Usuários

3. **Gerenciamento de usuários**
   - Nova página: `views/admin/usuarios.php`.
   - Novo controller atualizado: `services/usuario_controle.php`.
   - Permite criar, listar, editar e inativar usuários.
   - Permite alterar perfil/função pelo campo `id_funcao`.
   - A senha é obrigatória ao criar e opcional ao editar.

## Arquivos principais para substituir/adicionar

```txt
assets/css/techlounged.css
assets/js/techlounged.js
includes/topo.php
includes/admin_sidebar.php
views/admin/gerenciamento_sistema.php
views/admin/usuarios.php
services/usuario_controle.php
```

Também incluídos nesta versão:

```txt
services/registro_atividade_controle.php
models/RegistroAtividade.php
```

Esses dois arquivos mantêm a versão em que o controller usa melhor o model `RegistroAtividade`.
